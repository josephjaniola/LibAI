<?php
class Recommendation_model extends Model
{
    public function recommendForUser($user_type, $user_ref_id, $limit = 6)
    {
        $limit = max(1, (int) $limit);
        if (empty($this->getBorrowHistoryForUser($user_type, $user_ref_id, 1))) {
            return [];
        }

        $aiBooks = $this->getAiRecommendations($user_type, $user_ref_id, $limit);
        if (!empty($aiBooks)) {
            return $aiBooks;
        }

        return $this->getRuleBasedRecommendations($user_type, $user_ref_id, $limit);
    }

    private function getAiRecommendations($user_type, $user_ref_id, $limit)
    {
        if (empty(OPENAI_API_KEY) || !function_exists('curl_init')) {
            return [];
        }

        $history = $this->getBorrowHistoryForUser($user_type, $user_ref_id, 8);
        $catalog = $this->getCatalogForAiPrompt(30);
        $userProfile = $this->getUserProfile($user_type, $user_ref_id);

        if (empty($catalog)) {
            return [];
        }

        $prompt = [
            [
                'role' => 'system',
                'content' => 'You are a helpful librarian AI that recommends books for library users. Return valid JSON only. Format: {"recommended_ids":[id1,id2,...]}. Select only book IDs that exist in the given catalog. Keep recommendations relevant to the user profile and reading history, and prioritize books with higher borrow_count when they fit the user interest.'
            ],
            [
                'role' => 'user',
                'content' => json_encode([
                    'user_type' => $user_type,
                    'user_profile' => $userProfile,
                    'recent_history' => $history,
                    'catalog' => $catalog,
                    'required_count' => (int) $limit,
                ], JSON_PRETTY_PRINT)
            ]
        ];

        $payload = [
            'model' => OPENAI_MODEL,
            'temperature' => 0.4,
            'messages' => $prompt,
            'response_format' => ['type' => 'json_object']
        ];

        $ch = curl_init('https://api.openai.com/v1/chat/completions');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . OPENAI_API_KEY,
            ],
            CURLOPT_TIMEOUT => 30,
            CURLOPT_CONNECTTIMEOUT => 15,
        ]);

        $response = curl_exec($ch);
        if ($response === false) {
            curl_close($ch);
            return [];
        }

        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode >= 400) {
            return [];
        }

        $decoded = json_decode($response, true);
        $content = $decoded['choices'][0]['message']['content'] ?? '';
        if (empty($content)) {
            return [];
        }

        $ids = $this->extractAiBookIds($content);
        if (empty($ids)) {
            return [];
        }

        $ids = array_values(array_unique(array_filter(array_map('intval', $ids))));
        if (empty($ids)) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $sql = 'SELECT b.*, c.name AS category_name, p.name AS publisher_name, COALESCE(pop.borrow_count, 0) AS borrow_count FROM books b LEFT JOIN categories c ON b.category_id = c.id LEFT JOIN publishers p ON b.publisher_id = p.id LEFT JOIN (SELECT book_id, COUNT(*) AS borrow_count FROM borrow_transactions GROUP BY book_id) pop ON pop.book_id = b.id WHERE b.id IN (' . $placeholders . ') AND b.status <> "archived" ORDER BY borrow_count DESC, b.created_at DESC';
        $stmt = $this->db->prepare($sql);
        $stmt->execute($ids);
        $books = $stmt->fetchAll();

        $ordered = [];
        foreach ($ids as $id) {
            foreach ($books as $book) {
                if ((int)$book['id'] === (int)$id) {
                    $ordered[] = $book;
                    break;
                }
            }
        }

        return array_slice($ordered, 0, $limit);
    }

    private function getRuleBasedRecommendations($user_type, $user_ref_id, $limit)
    {
        $borrowerRefId = $this->getBorrowerReference($user_type, $user_ref_id);
        $sql = 'SELECT b.category_id, COUNT(*) as cnt FROM borrow_transactions bt JOIN books b ON bt.book_id=b.id WHERE bt.borrower_type = :ut AND bt.borrower_ref_id = :uid GROUP BY b.category_id ORDER BY cnt DESC';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':ut' => $user_type, ':uid' => $borrowerRefId]);
        $cats = $stmt->fetchAll();

        $catIds = array_column($cats, 'category_id');
        if (count($catIds) > 0) {
            $placeholders = implode(',', array_fill(0, count($catIds), '?'));
            $sql2 = "SELECT b.*, c.name AS category_name, p.name AS publisher_name, COALESCE(pop.borrow_count, 0) AS borrow_count FROM books b LEFT JOIN categories c ON b.category_id = c.id LEFT JOIN publishers p ON b.publisher_id = p.id LEFT JOIN (SELECT book_id, COUNT(*) AS borrow_count FROM borrow_transactions GROUP BY book_id) pop ON pop.book_id = b.id WHERE b.category_id IN ($placeholders) AND b.status <> 'archived' ORDER BY borrow_count DESC, b.created_at DESC LIMIT " . (int) $limit;
            $stmt2 = $this->db->prepare($sql2);
            $stmt2->execute($catIds);
            $results = $stmt2->fetchAll();
            if (!empty($results)) {
                return $results;
            }
        }

        $profile = $this->getUserProfile($user_type, $user_ref_id);
        $interestTerms = [];
        foreach ([$profile['course'] ?? '', $profile['department'] ?? '', $profile['position'] ?? ''] as $value) {
            $value = trim((string) $value);
            if ($value !== '') {
                $interestTerms[] = $value;
            }
        }

        if (!empty($interestTerms)) {
            $conditions = [];
            $params = [];
            foreach ($interestTerms as $index => $term) {
                $fields = ['b.title', 'b.subtitle', 'b.description', 'b.keywords', 'c.name'];
                $fieldConditions = [];
                foreach ($fields as $fieldIndex => $field) {
                    $placeholder = ':interest' . $index . '_' . $fieldIndex;
                    $fieldConditions[] = $field . ' LIKE ' . $placeholder;
                    $params[$placeholder] = '%' . $term . '%';
                }
                $conditions[] = '(' . implode(' OR ', $fieldConditions) . ')';
            }

            $sql4 = 'SELECT b.*, c.name AS category_name, p.name AS publisher_name, COALESCE(pop.borrow_count, 0) AS borrow_count FROM books b LEFT JOIN categories c ON b.category_id = c.id LEFT JOIN publishers p ON b.publisher_id = p.id LEFT JOIN (SELECT book_id, COUNT(*) AS borrow_count FROM borrow_transactions GROUP BY book_id) pop ON pop.book_id = b.id WHERE b.status <> "archived" AND (' . implode(' OR ', $conditions) . ') ORDER BY borrow_count DESC, b.created_at DESC LIMIT ' . (int) $limit;
            $stmt4 = $this->db->prepare($sql4);
            $stmt4->execute($params);
            $results = $stmt4->fetchAll();
            if (!empty($results)) {
                return $results;
            }
        }

        $sql3 = 'SELECT b.*, c.name AS category_name, p.name AS publisher_name, COUNT(bt.id) AS borrow_count FROM borrow_transactions bt JOIN books b ON bt.book_id = b.id LEFT JOIN categories c ON b.category_id = c.id LEFT JOIN publishers p ON b.publisher_id = p.id WHERE b.status <> "archived" GROUP BY b.id ORDER BY borrow_count DESC, b.created_at DESC LIMIT ' . (int) $limit;
        return $this->db->query($sql3)->fetchAll();
    }

    private function getBorrowerReference($user_type, $user_ref_id)
    {
        if ($user_type === 'student') {
            $stmt = $this->db->prepare('SELECT student_id FROM students WHERE id = :id LIMIT 1');
            $stmt->execute([':id' => $user_ref_id]);
            return $stmt->fetchColumn() ?: $user_ref_id;
        }

        if ($user_type === 'faculty') {
            $stmt = $this->db->prepare('SELECT faculty_id FROM faculty WHERE id = :id LIMIT 1');
            $stmt->execute([':id' => $user_ref_id]);
            return $stmt->fetchColumn() ?: $user_ref_id;
        }

        return $user_ref_id;
    }

    private function getUserProfile($user_type, $user_ref_id)
    {
        if ($user_type === 'student') {
            $stmt = $this->db->prepare('SELECT student_id, firstname, lastname, course, year_level, email FROM students WHERE id = :id LIMIT 1');
            $stmt->execute([':id' => $user_ref_id]);
            return $stmt->fetch() ?: [];
        }

        if ($user_type === 'faculty') {
            $stmt = $this->db->prepare('SELECT faculty_id, firstname, lastname, department, position, email FROM faculty WHERE id = :id LIMIT 1');
            $stmt->execute([':id' => $user_ref_id]);
            return $stmt->fetch() ?: [];
        }

        return [];
    }

    private function getUserCourseOrDepartment($user_type, $user_ref_id)
    {
        if ($user_type === 'student') {
            $stmt = $this->db->prepare('SELECT course AS value FROM students WHERE id = :id LIMIT 1');
            $stmt->execute([':id' => $user_ref_id]);
            $row = $stmt->fetch();
            return $row['value'] ?? null;
        }

        if ($user_type === 'faculty') {
            $stmt = $this->db->prepare('SELECT department AS value FROM faculty WHERE id = :id LIMIT 1');
            $stmt->execute([':id' => $user_ref_id]);
            $row = $stmt->fetch();
            return $row['value'] ?? null;
        }

        return null;
    }

    private function getBorrowHistoryForUser($user_type, $user_ref_id, $limit)
    {
        if ($user_type !== 'student' && $user_type !== 'faculty') {
            return [];
        }

        $sql = 'SELECT b.id, b.title, b.description, c.name AS category_name FROM borrow_transactions bt JOIN books b ON bt.book_id = b.id LEFT JOIN categories c ON b.category_id = c.id WHERE bt.borrower_type = :ut AND bt.borrower_ref_id = :uid ORDER BY bt.borrow_date DESC LIMIT ' . (int) $limit;
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':ut' => $user_type, ':uid' => $this->getBorrowerReference($user_type, $user_ref_id)]);
        return $stmt->fetchAll();
    }

    private function getCatalogForAiPrompt($limit)
    {
        $sql = 'SELECT b.id, b.title, b.description, b.keywords, c.name AS category_name, COALESCE(pop.borrow_count, 0) AS borrow_count FROM books b LEFT JOIN categories c ON b.category_id = c.id LEFT JOIN (SELECT book_id, COUNT(*) AS borrow_count FROM borrow_transactions GROUP BY book_id) pop ON pop.book_id = b.id WHERE b.status <> "archived" ORDER BY borrow_count DESC, b.created_at DESC LIMIT ' . (int) $limit;
        return $this->db->query($sql)->fetchAll();
    }

    private function extractAiBookIds($content)
    {
        $content = trim($content);
        if ($content === '') {
            return [];
        }

        $json = json_decode($content, true);
        if (is_array($json) && !empty($json['recommended_ids'])) {
            return $json['recommended_ids'];
        }

        if (is_array($json) && !empty($json['ids'])) {
            return $json['ids'];
        }

        if (is_array($json) && isset($json[0])) {
            $flattened = [];
            foreach ($json as $item) {
                if (is_array($item) && !empty($item['id'])) {
                    $flattened[] = $item['id'];
                }
            }
            if (!empty($flattened)) {
                return $flattened;
            }
        }

        preg_match_all('/\b(\d+)\b/', $content, $matches);
        return $matches[1] ?? [];
    }
}
