<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="The Indonesian Press - Admin Login" />
    <meta name="author" content="The Indonesian Press" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>Admin Login - The Indonesian Press</title>
    
    <!-- Styles -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <style>
        :root {
            --primary-color: #243c82;
            --secondary-color: #e63946;
        }
        
        html, body {
            height: 100%;
        }
        
        body {
            display: flex;
            align-items: center;
            background-color: #f5f7fb;
            font-family: 'Nunito', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        }
        
        .form-signin {
            max-width: 430px;
            padding: 15px;
            margin: auto;
        }
        
        .form-signin .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }
        
        .form-signin .card-header {
            background-color: var(--primary-color);
            color: white;
            text-align: center;
            padding: 25px;
            border-radius: 10px 10px 0 0;
        }
        
        .form-signin .logo {
            margin-bottom: 15px;
        }
        
        .form-signin .card-body {
            padding: 35px;
        }
        
        .form-signin .form-floating {
            margin-bottom: 20px;
        }
        
        .form-signin .form-control:focus {
            box-shadow: none;
            border-color: var(--primary-color);
        }
        
        .form-signin .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            padding: 10px;
            font-weight: 600;
        }
        
        .form-signin .btn-primary:hover {
            background-color: #1b2e63;
        }
        
        .form-signin .form-check {
            margin-bottom: 20px;
        }
        
        .form-signin .invalid-feedback {
            font-size: 0.85rem;
        }
        
        .btn-google {
            color: white;
            background-color: #ea4335;
            border-color: #ea4335;
        }
        
        .btn-google:hover {
            color: white;
            background-color: #d33426;
            border-color: #d33426;
        }
        
        .divider-text {
            position: relative;
            text-align: center;
            margin: 20px 0;
        }
        
        .divider-text span {
            padding: 0 15px;
            background-color: white;
            position: relative;
            z-index: 2;
            color: #6c757d;
        }
        
        .divider-text:after {
            content: "";
            position: absolute;
            width: 100%;
            border-bottom: 1px solid #ddd;
            top: 50%;
            left: 0;
            z-index: 1;
        }
        
        .brand-text {
            font-size: 1.5rem;
            font-weight: 700;
        }
    </style>
</head>

<body class="bg-light">
    <main class="form-signin w-100">
        <div class="card">
            <div class="card-header text-center">
                <div class="logo">
                    <img src="{{ asset('images/logo.png') }}" alt="The Indonesian Press" height="60">
                </div>
                <h4 class="brand-text">The Indonesian Press</h4>
                <div class="text-white-50">Admin Dashboard</div>
            </div>
            <div class="card-body">
                @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif
                
                <h5 class="card-title text-center mb-4">Sign In to Your Account</h5>
                
                <form method="POST" action="{{ route('admin.login.post') }}">
                    @csrf
                    
                    <div class="form-floating">
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" placeholder="name@example.com" value="{{ old('email') }}" required autofocus>
                        <label for="email">Email address</label>
                        @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="form-floating">
                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" placeholder="Password" required>
                        <label for="password">Password</label>
                        @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                        <label class="form-check-label" for="remember">
                            Remember me
                        </label>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button class="btn btn-primary text-uppercase" type="submit">
                            <i class="bi bi-box-arrow-in-right me-2"></i>Sign In
                        </button>
                    </div>
                </form>
                
                <div class="divider-text">
                    <span>OR</span>
                </div>
                
                <div class="d-grid gap-2">
                    <a href="#" class="btn btn-google">
                        <i class="bi bi-google me-2"></i>Sign in with Google
                    </a>
                </div>
                
                <div class="text-center mt-4">
                    <a href="{{ url('/') }}" class="text-decoration-none">
                        <i class="bi bi-arrow-left me-1"></i>Back to Website
                    </a>
                </div>
            </div>
        </div>
        <div class="text-center mt-3 text-muted">
            <small>&copy; The Indonesian Press {{ date('Y') }}</small>
        </div>
    </main>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>