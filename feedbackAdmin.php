
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Feedback</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Roboto', sans-serif;
        }

        body {
            display: flex;
            height: 100vh;
            overflow: hidden;
        }

        /* Sidebar styling */
        .sidebar {
            width: 250px;
            background-color: #39B7B7;
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .sidebar .logo {
            display: flex;
            align-items: center;
            padding: 20px;
        }

        .sidebar .logo img {
            width: 40px;
            margin-right: 10px;
        }

        .sidebar .logo h1 {
            font-size: 18px;
            font-weight: 700;
        }

        .sidebar nav {
            flex-grow: 1;
        }

        .sidebar nav a {
            display: block;
            padding: 15px 20px;
            color: white;
            text-decoration: none;
            font-weight: 500;
        }

        .sidebar nav a:hover, .sidebar nav a.active {
            background-color: #39B7B7;
        }

        .sidebar .logout {
            padding: 15px 20px;
            text-align: center;
            background-color: #39B7B7;
            cursor: pointer;
        }

        /* Main content styling */
        .main-content {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }

        .header {
            background-color: #39B7B7;
            padding: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: white;
        }

        .header .title {
            font-size: 18px;
            font-weight: 700;
        }

        .header .user {
            display: flex;
            align-items: center;
        }

        .header .user img {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            margin-left: 10px;
        }

        .content {
            padding: 20px;
            overflow-y: auto;
        }

        .content h2 {
            margin-bottom: 20px;
            font-weight: 700;
            font-size: 24px;
        }

        .feedback-section {
            display: flex;
            gap: 20px;
        }

        .feedback-list {
            flex: 2;
        }

        .feedback-card {
            background-color: #F9F9F9;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .feedback-card h3 {
            font-size: 16px;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .feedback-card p {
            font-size: 14px;
        }

        .feedback-form {
            flex: 1;
            background-color: #F9F9F9;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .feedback-form h3 {
            font-size: 16px;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .feedback-form textarea {
            width: 100%;
            height: 100px;
            margin-bottom: 10px;
            padding: 10px;
            border: 1px solid #CCC;
            border-radius: 5px;
            resize: none;
        }

        .feedback-form button {
            width: 100%;
            padding: 10px;
            background-color: #39B7B7;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 14px;
            cursor: pointer;
        }

        .footer {
            text-align: center;
            padding: 10px;
            background-color: #F1F1F1;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <div>
            <div class="logo">
                <img src="img/favicon.png" alt="Logo">
                <h1>Admin Panel</h1>
            </div>
            <nav>
                <a href="#" class="active">Dashboard</a>
                <a href="#">Users</a>
                <a href="#">Content</a>
                <a href="#">Feedback</a>
            </nav>
        </div>
        <div class="logout">LOG OUT</div>
    </div>

    <div class="main-content">
        <div class="header">
            <div class="title">Feedback Management</div>
            <div class="user">
                <span>Admin</span>
                <img src="img/admin-icon.png" alt="Admin Icon">
            </div>
        </div>
        <div class="content">
            <div class="feedback-section">
                <div class="feedback-list">
                    <div class="feedback-card">
                        <h3>Admin Feedback</h3>
                        <p>Posted on: 2025-01-01</p>
                        <p>Thank you for sharing your thoughts! We’ve taken note of your feedback and are working to make improvements to the platform.</p>
                    </div>
                    <div class="feedback-card">
                        <h3>Admin Response</h3>
                        <p>Posted on: 2024-12-15</p>
                        <p>We’re happy to hear you found the content helpful! Please let us know if there are any specific topics you’d like to see covered.</p>
                    </div>
                </div>
                <div class="feedback-form">
                    <h3>Add Admin Notes</h3>
                    <textarea placeholder="Write your response or notes here..."></textarea>
                    <button>Submit Note</button>
                </div>
            </div>
        </div>
        <div class="footer">
            &copy; 2025 Admin Panel | All Rights Reserved
        </div>
    </div>
</body>
</html>
