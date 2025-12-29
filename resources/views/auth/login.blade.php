<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - RAPI PVC</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        @keyframes gradient {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        
        .animated-gradient {
            background: linear-gradient(-45deg, #667eea, #764ba2, #f093fb, #4facfe);
            background-size: 400% 400%;
            animation: gradient 15s ease infinite;
        }
        
        .glass-effect {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }
        
        .input-focus:focus {
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
            border-color: #667eea;
        }
    </style>
</head>

<body class="animated-gradient min-h-screen flex items-center justify-center p-3 sm:p-4 md:p-6">
        @if ($errors->has('email'))
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Login Gagal',
                        text: 'Email atau Password salah',
                        confirmButtonColor: '#6366f1',
                    });
                });
            </script>
        @endif
    <div class="w-full max-w-6xl grid grid-cols-1 lg:grid-cols-2 rounded-3xl overflow-hidden shadow-2xl">
        
        <!-- Left Side - Branding (Desktop Only) -->
        <div class="hidden lg:flex flex-col justify-center items-center bg-gradient-to-br from-indigo-600 via-purple-600 to-indigo-800 p-12 text-white relative overflow-hidden">
            
            <div class="absolute top-0 left-0 w-72 h-72 bg-white opacity-5 rounded-full -translate-x-1/2 -translate-y-1/2"></div>
            <div class="absolute bottom-0 right-0 w-96 h-96 bg-white opacity-5 rounded-full translate-x-1/2 translate-y-1/2"></div>
            
            <div class="relative z-10 text-center">
                <div class="mb-8 flex justify-center">
                    <img src="{{ asset('images/logo-rapi.png') }}" 
                         class="w-40 h-40 object-contain drop-shadow-2xl" 
                         alt="Logo RAPI PVC">
                </div>
                
                <h1 class="text-4xl font-bold mb-4">RAPI PVC</h1>
                <p class="text-lg text-indigo-100 mb-8">Sistem Manajemen Keuangan</p>
                
                <div class="space-y-4 text-left">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center">✓</div>
                        <span>Manajemen Keuangan Terintegrasi</span>
                    </div>
                    
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center">✓</div>
                        <span>Laporan Real-time</span>
                    </div>
                    
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center">✓</div>
                        <span>Aman & Terpercaya</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side - Login Form -->
        <div class="glass-effect p-6 sm:p-8 md:p-10 lg:p-12 flex flex-col justify-center">
            
            <!-- Logo Mobile & Tablet -->
            <div class="flex justify-center mb-6 lg:hidden">
                <img src="{{ asset('images/logo-rapi.png') }}" 
                     class="w-52 h-52 xl:w-50 xl:h-50 object-contain drop-shadow-2xl" 
                     alt="Logo RAPI PVC">
            </div>
            
            <div class="mb-6 text-center sm:text-left">
                <h2 class="text-2xl sm:text-3xl font-bold text-gray-800 mb-2">
                    Selamat Datang
                </h2>
                <p class="text-gray-600 text-sm sm:text-base">
                    Silakan login untuk melanjutkan
                </p>
            </div>
            
            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf
                
                <!-- Email -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                        Email
                    </label>
                    <input type="email" 
                           name="email" 
                           required 
                           class="w-full px-4 py-3 border rounded-xl focus:outline-none input-focus" 
                           placeholder="nama@email.com">
                </div>
                
                <!-- Password -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                        Password
                    </label>
                    <input type="password" 
                           name="password" 
                           required 
                           class="w-full px-4 py-3 border rounded-xl focus:outline-none input-focus" 
                           placeholder="••••••••">
                </div>
                
                <!-- Remember Me -->
                <div class="flex items-center">
                    <input type="checkbox" 
                           name="remember" 
                           class="mr-2">
                    <span class="text-sm text-gray-600">Ingat Saya</span>
                </div>
                
                <!-- Submit Button -->
                <button type="submit" 
                        class="w-full bg-gradient-to-r from-indigo-600 to-purple-600 text-white py-3 rounded-xl font-semibold shadow hover:shadow-lg transition">
                    Masuk
                </button>
            </form>
            
            <div class="mt-6 text-center text-xs sm:text-sm text-gray-500">
                © {{ date('Y') }} RAPI PVC. All rights reserved.
            </div>
        </div>
    </div>
</body>
</html>