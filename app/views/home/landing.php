<section class="landing-hero py-5" id="home">
  <div class="container">
    <?php if (!empty($_SESSION['flash'])): ?>
      <div class="alert alert-success mb-4"><?php echo e($_SESSION['flash']); unset($_SESSION['flash']); ?></div>
    <?php endif; ?>
    <?php if (!empty($_SESSION['flash_error'])): ?>
      <div class="alert alert-danger mb-4"><?php echo e($_SESSION['flash_error']); unset($_SESSION['flash_error']); ?></div>
    <?php endif; ?>
    <div class="row align-items-center g-4">
      <div class="col-lg-8">
        <p class="eyebrow">College of Information Technology</p>
        <h1 class="display-5 fw-bold">LibAI: Improving the Manual Library Borrowing and Book Inventory Process through an AI-Powered Smart Library, RFID-Based Inventory Management, and Mobile-Accessible Library System for Cebu Eastern College</h1>
        <p class="lead">A professional digital library platform for efficient borrowing, inventory tracking, and mobile access.</p>
        <div class="mt-4 d-flex flex-wrap gap-2">
          <a href="?url=auth/login" class="btn btn-primary btn-lg">Login</a>
          <a href="?url=register/student" class="btn btn-outline-primary btn-lg">Register Student</a>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="landing-section py-5" id="features">
  <div class="container">
    <div class="section-heading">
      <p class="eyebrow">Features</p>
      <h2 class="h3 mb-3">What users can do</h2>
      <p class="text-muted">LibAI provides an intelligent library experience for students, faculty, librarians, and administrators.</p>
    </div>
    <div class="row gy-4">
      <div class="col-md-6 col-xl-3">
        <div class="card p-4 h-100">
          <h5>Search & browse books</h5>
          <p>Find available titles instantly and explore categories with a responsive catalog.</p>
        </div>
      </div>
      <div class="col-md-6 col-xl-3">
        <div class="card p-4 h-100">
          <h5>Reserve & borrow</h5>
          <p>Reserve books from your device, then borrow or return with RFID-assisted processing.</p>
        </div>
      </div>
      <div class="col-md-6 col-xl-3">
        <div class="card p-4 h-100">
          <h5>Receive alerts</h5>
          <p>Get due-date reminders, reservation updates, and library notifications in one place.</p>
        </div>
      </div>
      <div class="col-md-6 col-xl-3">
        <div class="card p-4 h-100">
          <h5>View recommendations</h5>
          <p>See AI-powered suggestions based on your borrowing history and academic interests.</p>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="landing-section py-5" id="about">
  <div class="container">
    <div class="section-heading">
      <p class="eyebrow">About</p>
      <h2 class="h3 mb-3">How LibAI helps Cebu Eastern College</h2>
      <p class="text-muted">A modern library system that replaces manual records with real-time inventory, mobile access, and smart workflows.</p>
    </div>
    <div class="row gy-4">
      <div class="col-lg-6">
        <div class="card p-4 h-100">
          <h5>For students</h5>
          <p>Search and reserve books, track borrow history, and receive return reminders on any device.</p>
        </div>
      </div>
      <div class="col-lg-6">
        <div class="card p-4 h-100">
          <h5>For librarians</h5>
          <p>Process loans quickly, approve reservations, track inventory, and manage RFID-enabled transactions.</p>
        </div>
      </div>
      <div class="col-lg-6">
        <div class="card p-4 h-100">
          <h5>For administrators</h5>
          <p>Generate reports, monitor library performance, and access accurate usage analytics.</p>
        </div>
      </div>
      <div class="col-lg-6">
        <div class="card p-4 h-100">
          <h5>AI-powered support</h5>
          <p>Get book suggestions based on program, interests, and borrowing behavior to support learning.</p>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="landing-section py-5" id="contact">
  <div class="container">
    <div class="section-heading">
      <p class="eyebrow">Contact</p>
      <h2 class="h3 mb-3">Send a message to the admin</h2>
      <p class="text-muted">Use this section to request help, report issues, or ask questions about the library system.</p>
    </div>
    <div class="row gy-4">
      <div class="col-lg-6">
        <div class="card p-4 h-100">
          <h5>What you can do</h5>
          <ul>
            <li>Ask for assistance with login or registration.</li>
            <li>Report missing or damaged books.</li>
            <li>Request reservations or book recommendations.</li>
            <li>Send general feedback to the library admin team.</li>
          </ul>
        </div>
      </div>
      <div class="col-lg-6">
        <div class="card p-4 h-100">
          <h5>Contact form</h5>
          <form action="?url=home/contact" method="post">
            <input type="hidden" name="_csrf" value="<?php echo e(generate_csrf_token()); ?>">
            <div class="mb-3">
              <label class="form-label">Your Name</label>
              <input type="text" name="name" class="form-control" placeholder="Enter your name" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Your Email</label>
              <input type="email" name="email" class="form-control" placeholder="Enter your email" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Message</label>
              <textarea name="message" class="form-control" rows="4" placeholder="Write your message to the admin" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Send Message</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</section>
