<!-- Mobile Menu Toggle -->
<button class="mobile-menu-toggle" onclick="toggleSidebar()">
    <i class="bi bi-list"></i>
</button>

<!-- Sidebar -->
<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <h1 class="sidebar-title">School Admin</h1>
    </div>

    <nav class="sidebar-nav">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a href="dashboard.php" class="nav-link active">
                    <i class="bi bi-house-door-fill"></i>
                    Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a href="student-management.php" class="nav-link">
                    <i class="bi bi-people-fill"></i>
                    Student Management
                </a>
            </li>
            <li class="nav-item">
                <a href="staff-management.php" class="nav-link">
                    <i class="bi bi-person-badge-fill"></i>
                    Staff Management
                </a>
            </li>
            <li class="nav-item">
                <a href="class-management.php" class="nav-link">
                    <i class="bi bi-book-fill"></i>
                    Class Management
                </a>
            </li>
            <li class="nav-item">
                <a href="subject-management.php" class="nav-link">
                    <i class="bi bi-journal-text"></i>
                    Subject Management
                </a>
            </li>
            <li class="nav-item">
                <a href="fee-management.php" class="nav-link">
                    <i class="bi bi-credit-card-fill"></i>
                    Fee Management
                </a>
            </li>
            <li class="nav-item">
                <a href="result-management.php" class="nav-link">
                    <i class="bi bi-bar-chart-fill"></i>
                    Result Management
                </a>
            </li>
            <li class="nav-item">
                <a href="view-results.php" class="nav-link">
                    <i class="bi bi-graph-up"></i>
                    View Results
                </a>
            </li>
        </ul>
    </nav>

    <div class="sidebar-footer">
        <a href="includes/logout.php" class="logout-link">
            <i class="bi bi-box-arrow-right"></i>
            Logout
        </a>
    </div>
</aside>

