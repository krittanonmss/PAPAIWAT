<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
            background: #f5f7fb;
            color: #111827;
        }

        .wrap {
            max-width: 960px;
            margin: 0 auto;
            padding: 32px 20px;
        }

        .card {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 24px;
        }

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            margin-bottom: 20px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 40px;
            padding: 0 14px;
            border: 0;
            border-radius: 8px;
            background: #111827;
            color: #ffffff;
            cursor: pointer;
        }

        form {
            margin: 0;
        }
    </style>
</head>
<body>
    <div class="wrap">
        <div class="topbar">
            <h1>Admin Dashboard</h1>

            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button type="submit" class="btn">ออกจากระบบ</button>
            </form>
        </div>

        <div class="card">
            <p>เข้าสู่ระบบสำเร็จ</p>
            <p>ยินดีต้อนรับ, {{ auth('admin')->user()?->username }}</p>
        </div>
    </div>
</body>
</html>