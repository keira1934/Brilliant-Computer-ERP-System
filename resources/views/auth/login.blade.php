<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Briliant Computer ERP</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Inter', -apple-system, sans-serif;
            min-height: 100vh;
            display: flex;
            background: #0a0e1a;
            overflow: hidden;
        }

        /* ── Left Panel: Brand / Visual ── */
        .login-brand {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 60px 80px;
            position: relative;
            background: linear-gradient(135deg, #0f1629 0%, #1a1f3a 50%, #0f1629 100%);
            overflow: hidden;
        }
        .login-brand::before {
            content: '';
            position: absolute;
            top: -120px; right: -120px;
            width: 500px; height: 500px;
            background: radial-gradient(circle, rgba(99,102,241,0.12) 0%, transparent 70%);
            border-radius: 50%;
        }
        .login-brand::after {
            content: '';
            position: absolute;
            bottom: -100px; left: -80px;
            width: 400px; height: 400px;
            background: radial-gradient(circle, rgba(6,182,212,0.08) 0%, transparent 70%);
            border-radius: 50%;
        }
        .brand-logo {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 48px;
            position: relative;
            z-index: 1;
        }
        .brand-logo-icon {
            width: 52px; height: 52px;
            background: linear-gradient(135deg, #6366f1, #06b6d4);
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: #fff;
        }
        .brand-logo-text h1 {
            font-size: 22px;
            font-weight: 700;
            color: #e2e8f0;
            letter-spacing: -0.3px;
        }
        .brand-logo-text span {
            font-size: 12px;
            color: #64748b;
            font-weight: 500;
            letter-spacing: 1.5px;
            text-transform: uppercase;
        }
        .brand-tagline {
            position: relative;
            z-index: 1;
        }
        .brand-tagline h2 {
            font-size: 38px;
            font-weight: 800;
            color: #e2e8f0;
            line-height: 1.2;
            letter-spacing: -0.8px;
            margin-bottom: 16px;
        }
        .brand-tagline h2 span {
            background: linear-gradient(135deg, #6366f1, #06b6d4);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .brand-tagline p {
            font-size: 15px;
            color: #64748b;
            line-height: 1.7;
            max-width: 440px;
        }
        .brand-features {
            display: flex;
            flex-direction: column;
            gap: 14px;
            margin-top: 40px;
            position: relative;
            z-index: 1;
        }
        .brand-feature {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 13px;
            color: #94a3b8;
        }
        .brand-feature i {
            color: #6366f1;
            font-size: 16px;
            width: 20px;
        }

        /* ── Right Panel: Login Form ── */
        .login-form-panel {
            width: 480px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 60px 56px;
            background: #111827;
            border-left: 1px solid rgba(99,102,241,0.1);
        }
        .login-header {
            margin-bottom: 36px;
        }
        .login-header h3 {
            font-size: 24px;
            font-weight: 700;
            color: #e2e8f0;
            margin-bottom: 6px;
        }
        .login-header p {
            font-size: 14px;
            color: #64748b;
        }

        .form-group {
            margin-bottom: 22px;
        }
        .form-group label {
            display: block;
            font-size: 12px;
            font-weight: 600;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin-bottom: 8px;
        }
        .input-wrapper {
            position: relative;
        }
        .input-wrapper i {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #475569;
            font-size: 16px;
            transition: color 0.2s;
        }
        .input-wrapper input {
            width: 100%;
            padding: 13px 14px 13px 44px;
            background: #1e293b;
            border: 1.5px solid #1e293b;
            border-radius: 10px;
            color: #e2e8f0;
            font-size: 14px;
            font-family: 'Inter', sans-serif;
            transition: all 0.2s;
        }
        .input-wrapper input:focus {
            outline: none;
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99,102,241,0.15);
        }
        .input-wrapper input:focus + i,
        .input-wrapper input:focus ~ i {
            color: #6366f1;
        }
        .input-wrapper input::placeholder {
            color: #475569;
        }

        .form-options {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 28px;
        }
        .remember-me {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            color: #94a3b8;
            cursor: pointer;
        }
        .remember-me input[type="checkbox"] {
            width: 16px; height: 16px;
            accent-color: #6366f1;
            cursor: pointer;
        }

        .btn-login {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #6366f1, #4f46e5);
            color: #fff;
            border: none;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 600;
            font-family: 'Inter', sans-serif;
            cursor: pointer;
            transition: all 0.2s;
            letter-spacing: 0.3px;
        }
        .btn-login:hover {
            background: linear-gradient(135deg, #7c3aed, #6366f1);
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(99,102,241,0.3);
        }
        .btn-login:active {
            transform: translateY(0);
        }

        .alert-error {
            background: rgba(239,68,68,0.1);
            border: 1px solid rgba(239,68,68,0.2);
            border-radius: 10px;
            padding: 12px 16px;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 13px;
            color: #f87171;
        }
        .alert-error i {
            font-size: 16px;
        }

        .login-footer {
            margin-top: 40px;
            text-align: center;
            font-size: 12px;
            color: #475569;
        }

        /* ── Responsive ── */
        @media (max-width: 1024px) {
            .login-brand { display: none; }
            .login-form-panel {
                width: 100%;
                max-width: 460px;
                margin: 0 auto;
                border-left: none;
            }
            body { justify-content: center; }
        }
    </style>
</head>
<body>
    <!-- Brand Panel -->
    <div class="login-brand">
        <div class="brand-logo">
            <div class="brand-logo-icon"><i class="bi bi-pc-display-horizontal"></i></div>
            <div class="brand-logo-text">
                <h1>Briliant Computer</h1>
                <span>ERP System</span>
            </div>
        </div>
        <div class="brand-tagline">
            <h2>Enterprise<br><span>Resource Planning</span></h2>
            <p>Integrated Accounting Information System with double-entry bookkeeping, internal controls, and audit-ready financial reporting.</p>
        </div>
        <div class="brand-features">
            <div class="brand-feature"><i class="bi bi-shield-check"></i>Role-Based Access Control</div>
            <div class="brand-feature"><i class="bi bi-journal-bookmark-fill"></i>Double-Entry Bookkeeping</div>
            <div class="brand-feature"><i class="bi bi-graph-up-arrow"></i>Real-time Financial Reports</div>
            <div class="brand-feature"><i class="bi bi-clock-history"></i>Complete Audit Trail</div>
            <div class="brand-feature"><i class="bi bi-box-seam"></i>Inventory & Supply Chain</div>
        </div>
    </div>

    <!-- Login Form -->
    <div class="login-form-panel">
        <div class="login-header">
            <h3>Sign In</h3>
            <p>Access the ERP system with your credentials</p>
        </div>

        @if(session('error'))
            <div class="alert-error">
                <i class="bi bi-exclamation-circle-fill"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="form-group">
                <label for="email">Email Address</label>
                <div class="input-wrapper">
                    <input type="email" id="email" name="email" value="{{ old('email') }}"
                           placeholder="Enter your email" required autofocus>
                    <i class="bi bi-envelope"></i>
                </div>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <div class="input-wrapper">
                    <input type="password" id="password" name="password"
                           placeholder="Enter your password" required>
                    <i class="bi bi-lock"></i>
                </div>
            </div>

            <div class="form-options">
                <label class="remember-me">
                    <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                    Remember me
                </label>
            </div>

            <button type="submit" class="btn-login">
                <i class="bi bi-box-arrow-in-right" style="margin-right:6px"></i>Sign In
            </button>
        </form>

        <div class="login-footer">
            &copy; {{ date('Y') }} Briliant Computer — Accounting ERP System
        </div>
    </div>
</body>
</html>
