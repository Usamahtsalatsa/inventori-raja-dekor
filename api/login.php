<?php
session_start();
include 'koneksi.php';

if (isset($_SESSION['login']) && $_SESSION['login'] == true) {
    header("Location: /api/dashboard.php");
    exit();
}

$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = md5(mysqli_real_escape_string($koneksi, $_POST['password']));
    
    $query = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
    $result = mysqli_query($koneksi, $query);
    
    if (mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);
        $_SESSION['login'] = true;
        $_SESSION['id_user'] = $user['id_user'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
        $_SESSION['level'] = $user['level'];
        catatLog($koneksi, 'Login', "User {$user['username']} berhasil login");
        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Username atau password salah!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Sistem Inventori Raja Dekor</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <style>
        /* Canvas partikel di background */
        #particleCanvas {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 0;
        }

        /* Layout utama – lebih rapat ke tengah */
        .auth-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            position: relative;
            z-index: 1;
            background: transparent;
        }

        .login-layout {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 2rem;
            max-width: 1000px;
            width: 100%;
            flex-wrap: wrap;
            margin: 0 auto;
        }

        /* Sapaan di kiri: slide from left + fade */
        .greeting-left {
            flex: 1;
            min-width: 280px;
            opacity: 0;
            transform: translateX(-40px);
            animation: slideInLeft 1s ease forwards;
        }

        .greeting-left h1 {
            font-size: 2.8rem;
            font-weight: 800;
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            margin-bottom: 1rem;
            line-height: 1.2;
        }

        .greeting-left p {
            color: var(--text-secondary);
            font-size: 1.1rem;
        }

        @keyframes slideInLeft {
            0% {
                opacity: 0;
                transform: translateX(-40px);
            }
            100% {
                opacity: 1;
                transform: translateX(0);
            }
        }

        /* Form login: lebih transparan (salju terlihat), tapi tetap gelap */
        .auth-card {
            flex: 1;
            min-width: 360px;
            max-width: 420px;
            margin: 0;
            opacity: 0;
            transform: translateY(40px);
            animation: slideUpFade 1s ease forwards;
            background: rgba(8, 10, 20, 0.55) !important;  /* lebih transparan, latar gelap */
            backdrop-filter: blur(16px);
            border: 1px solid rgba(168, 85, 247, 0.4);
            transition: all 0.3s cubic-bezier(0.2, 0.9, 0.4, 1.1);
        }

        /* Hover interaktif lebih soft (cahaya tidak terlalu terang) */
        .auth-card:hover {
            transform: translateY(-4px) scale(1.005);
            background: rgba(8, 10, 20, 0.65) !important;
            border-color: rgba(168, 85, 247, 0.7);
            box-shadow: 0 12px 28px rgba(0, 0, 0, 0.25), 0 0 12px rgba(168, 85, 247, 0.2);
        }

        .auth-card .auth-header,
        .auth-card .auth-body {
            background: transparent;
        }

        @keyframes slideUpFade {
            0% {
                opacity: 0;
                transform: translateY(40px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Responsive */
        @media (max-width: 768px) {
            .login-layout {
                flex-direction: column;
                text-align: center;
                gap: 2rem;
            }
            .greeting-left {
                text-align: center;
                transform: translateX(0);
                animation: fadeInOnly 1s ease forwards;
            }
            @keyframes fadeInOnly {
                from { opacity: 0; }
                to { opacity: 1; }
            }
        }
    </style>
</head>
<body>

<canvas id="particleCanvas"></canvas>

<div class="auth-wrapper">
    <div class="login-layout">
        <!-- Sapaan -->
        <div class="greeting-left">
            <h1>Selamat datang,<br>Administrator!</h1>
            <p>Kelola inventori Raja Dekor<br>dengan sistem modern & terintegrasi.</p>
        </div>

        <!-- Form login dengan transparansi pas dan hover lembut -->
        <div class="auth-card">
            <div class="auth-header">
                <div class="auth-logo"><i class="fas fa-crown"></i></div>
                <h2>RAJA DEKOR</h2>
                <p>Silakan login untuk melanjutkan</p>
            </div>
            <div class="auth-body">
                <?php if ($error): ?>
                    <div class="alert-premium alert-danger-premium">
                        <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" autocomplete="off">
                    <div style="margin-bottom: 18px;">
                        <label class="form-label-premium">Username</label>
                        <input type="text" name="username" class="form-glass" placeholder="Masukkan username" required autofocus>
                    </div>
                    <div style="margin-bottom: 24px;">
                        <label class="form-label-premium">Password</label>
                        <input type="password" name="password" class="form-glass" placeholder="Masukkan password" required>
                    </div>
                    <button type="submit" class="btn-premium btn-primary-glow" style="width:100%; padding: 12px; font-size: 14px;">
                        <i class="fas fa-sign-in-alt"></i> Masuk
                    </button>
                </form>
                <div style="text-align:center; margin-top: 20px; padding-top: 20px; border-top: 1px solid var(--border-light);">
                    <a href="register.php" class="auth-link">Belum punya akun? <span style="color: var(--accent-violet);">Daftar disini</span></a>
                </div>
                <p class="auth-demo" style="text-align:center; margin-top: 14px;">Demo: admin / admin123</p>
            </div>
        </div>
    </div>
</div>

<script>
    (function() {
        const canvas = document.getElementById('particleCanvas');
        const ctx = canvas.getContext('2d');
        let width, height;
        let particles = [];
        let mouseX = -1000, mouseY = -1000;
        const PARTICLE_COUNT = 500;
        const REPEL_RADIUS = 300;
        const REPEL_FORCE = 1.8;

        function resizeCanvas() {
            width = window.innerWidth;
            height = window.innerHeight;
            canvas.width = width;
            canvas.height = height;
        }

        function updateMouse(e) {
            mouseX = e.clientX;
            mouseY = e.clientY;
        }

        window.addEventListener('resize', resizeCanvas);
        window.addEventListener('mousemove', updateMouse);
        window.addEventListener('mouseleave', () => { mouseX = -1000; mouseY = -1000; });

        class Particle {
            constructor() {
                this.reset();
            }
            reset() {
                this.x = Math.random() * width;
                this.y = Math.random() * height;
                this.size = Math.random() * 2 + 0.5;
                this.speedX = (Math.random() - 0.5) * 1.5;
                this.speedY = Math.random() * 1.5 + 0.8;
                this.color = `rgba(168, 85, 247, ${Math.random() * 0.3 + 0.15})`;
            }
            update() {
                this.x += this.speedX;
                this.y += this.speedY;

                if (this.y > height + 30 || this.x < -50 || this.x > width + 50) {
                    this.x = Math.random() * width;
                    this.y = -15;
                    this.speedX = (Math.random() - 0.5) * 1.5;
                    this.speedY = Math.random() * 1.5 + 0.8;
                    return;
                }

                if (mouseX > 0 && mouseX < width && mouseY > 0 && mouseY < height) {
                    const dx = this.x - mouseX;
                    const dy = this.y - mouseY;
                    const dist = Math.hypot(dx, dy);
                    if (dist < REPEL_RADIUS) {
                        const force = (REPEL_RADIUS - dist) / REPEL_RADIUS;
                        const angle = Math.atan2(dy, dx);
                        const moveX = Math.cos(angle) * force * REPEL_FORCE;
                        const moveY = Math.sin(angle) * force * REPEL_FORCE;
                        this.x += moveX;
                        this.y += moveY;
                    }
                }

                this.x = Math.min(Math.max(this.x, -50), width + 50);
                this.y = Math.min(Math.max(this.y, -15), height + 30);
            }
            draw() {
                ctx.beginPath();
                ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2);
                ctx.fillStyle = this.color;
                ctx.fill();
                ctx.shadowBlur = 4;
                ctx.shadowColor = 'rgba(168, 85, 247, 0.4)';
                ctx.fill();
                ctx.shadowBlur = 0;
            }
        }

        function initParticles() {
            particles = [];
            for (let i = 0; i < PARTICLE_COUNT; i++) {
                particles.push(new Particle());
            }
        }

        function animate() {
            ctx.clearRect(0, 0, width, height);
            for (let p of particles) {
                p.update();
                p.draw();
            }
            requestAnimationFrame(animate);
        }

        resizeCanvas();
        initParticles();
        animate();

        window.addEventListener('resize', () => {
            resizeCanvas();
            initParticles();
        });
    })();
</script>
</body>
</html>
