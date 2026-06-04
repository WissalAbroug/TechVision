<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>🔒 Sécurité — PathFinder AI</title>
  <!-- Outfit & Inter Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  
  <style>
    :root {
      --bg: #06060c;
      --card-bg: rgba(13, 12, 34, 0.45);
      --border: rgba(255, 255, 255, 0.08);
      --primary: #00f0ff;
      --secondary: #a78bfa;
      --success: #00ff87;
      --danger: #ff4a5a;
      --text: #f8fafc;
      --text-muted: #94a3b8;
    }

    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    body {
      font-family: 'Inter', sans-serif;
      background-color: var(--bg);
      color: var(--text);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      overflow: hidden;
      position: relative;
    }

    /* Ambient background glows */
    .glow-orb {
      position: absolute;
      border-radius: 50%;
      filter: blur(140px);
      z-index: 1;
      opacity: 0.45;
      animation: floatOrb 12s infinite alternate ease-in-out;
    }

    .orb-1 {
      width: 400px;
      height: 400px;
      background: radial-gradient(circle, var(--primary) 0%, transparent 70%);
      top: -100px;
      left: -100px;
    }

    .orb-2 {
      width: 500px;
      height: 500px;
      background: radial-gradient(circle, var(--secondary) 0%, transparent 70%);
      bottom: -150px;
      right: -150px;
      animation-delay: -6s;
    }

    @keyframes floatOrb {
      0% { transform: translate(0, 0) scale(1); }
      100% { transform: translate(60px, 40px) scale(1.15); }
    }

    .gate-container {
      position: relative;
      z-index: 10;
      width: 100%;
      max-width: 450px;
      padding: 1.5rem;
    }

    .gate-card {
      background: var(--card-bg);
      border: 1px solid var(--border);
      border-radius: 24px;
      padding: 3rem 2.5rem;
      backdrop-filter: blur(20px);
      -webkit-backdrop-filter: blur(20px);
      box-shadow: 0 20px 50px rgba(0, 240, 255, 0.05), inset 0 1px 0 rgba(255,255,255,0.1);
      text-align: center;
      position: relative;
      overflow: hidden;
    }

    .gate-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 4px;
      background: linear-gradient(90deg, var(--primary), var(--secondary));
    }

    .shield-icon {
      font-size: 3.5rem;
      margin-bottom: 1.5rem;
      display: inline-block;
      filter: drop-shadow(0 0 15px rgba(0, 240, 255, 0.4));
      animation: pulseIcon 2s infinite alternate ease-in-out;
    }

    @keyframes pulseIcon {
      0% { transform: scale(1); filter: drop-shadow(0 0 10px rgba(0, 240, 255, 0.3)); }
      100% { transform: scale(1.08); filter: drop-shadow(0 0 25px rgba(0, 240, 255, 0.6)); }
    }

    h1 {
      font-family: 'Outfit', sans-serif;
      font-size: 1.8rem;
      font-weight: 700;
      letter-spacing: -0.02em;
      margin-bottom: 0.75rem;
      background: linear-gradient(135deg, #fff 30%, var(--text-muted) 100%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
    }

    .desc {
      font-size: 0.92rem;
      color: var(--text-muted);
      line-height: 1.6;
      margin-bottom: 2rem;
    }

    .equation-box {
      background: rgba(255, 255, 255, 0.02);
      border: 1px solid rgba(255, 255, 255, 0.05);
      border-radius: 16px;
      padding: 1.25rem;
      font-family: 'Outfit', sans-serif;
      font-size: 2.2rem;
      font-weight: 700;
      color: var(--success);
      letter-spacing: 0.1em;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 0.75rem;
      margin-bottom: 2rem;
      box-shadow: inset 0 2px 10px rgba(0, 255, 135, 0.05);
      text-shadow: 0 0 15px rgba(0, 255, 135, 0.4);
    }

    .equation-box span.operator {
      color: var(--text);
      text-shadow: none;
    }

    form {
      display: flex;
      flex-direction: column;
      gap: 1rem;
    }

    .input-group {
      position: relative;
    }

    input[type="number"] {
      width: 100%;
      background: rgba(255, 255, 255, 0.03);
      border: 1px solid rgba(255, 255, 255, 0.12);
      border-radius: 12px;
      padding: 1rem;
      color: #fff;
      font-size: 1.1rem;
      font-weight: 600;
      text-align: center;
      outline: none;
      transition: all 0.3s ease;
      font-family: 'Outfit', sans-serif;
    }

    input[type="number"]::-webkit-outer-spin-button,
    input[type="number"]::-webkit-inner-spin-button {
      -webkit-appearance: none;
      margin: 0;
    }

    input[type="number"]:focus {
      border-color: var(--primary);
      background: rgba(0, 240, 255, 0.02);
      box-shadow: 0 0 20px rgba(0, 240, 255, 0.15);
    }

    .btn-verify {
      background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
      color: #06060c;
      border: none;
      border-radius: 12px;
      padding: 1rem;
      font-family: 'Outfit', sans-serif;
      font-size: 1.05rem;
      font-weight: 700;
      cursor: pointer;
      transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
      box-shadow: 0 4px 15px rgba(0, 240, 255, 0.2);
    }

    .btn-verify:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 25px rgba(0, 240, 255, 0.4);
      filter: brightness(1.1);
    }

    .btn-verify:active {
      transform: translateY(0);
    }

    .error-msg {
      margin-top: 1rem;
      font-size: 0.88rem;
      color: var(--danger);
      font-weight: 500;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 0.5rem;
      animation: shake 0.4s ease-in-out;
    }

    @keyframes shake {
      0%, 100% { transform: translateX(0); }
      20%, 60% { transform: translateX(-6px); }
      40%, 80% { transform: translateX(6px); }
    }
  </style>
</head>
<body>

  <div class="glow-orb orb-1"></div>
  <div class="glow-orb orb-2"></div>

  <!-- Ambient Cyberpunk Particles Background -->
  <canvas id="bg-neural-canvas" style="position:fixed;top:0;left:0;width:100vw;height:100vh;z-index:2;pointer-events:none;"></canvas>

  <div class="gate-container">
    <div class="gate-card">
      <div class="shield-icon">🛡️</div>
      <h1>Portail de Sécurité</h1>
      <p class="desc">Veuillez résoudre l'équation ci-dessous pour prouver que vous êtes un humain et accéder à la plateforme.</p>
      
      <div class="equation-box">
        <span><?= $_SESSION['captcha_num1'] ?></span>
        <span class="operator">+</span>
        <span><?= $_SESSION['captcha_num2'] ?></span>
        <span class="operator">=</span>
        <span class="operator">?</span>
      </div>

      <form method="POST" action="">
        <div class="input-group">
          <input type="number" name="captcha_answer" placeholder="Entrez la réponse..." required autofocus autocomplete="off">
        </div>
        <button type="submit" class="btn-verify">Accéder à PathFinder AI</button>
      </form>

      <?php if (isset($captcha_error)): ?>
        <div class="error-msg">
          ⚠️ Réponse incorrecte. Veuillez réessayer.
        </div>
      <?php endif; ?>
    </div>
  </div>

  <script>
    // Particle simulation for standalone captcha gate
    (function() {
      const canvas = document.getElementById('bg-neural-canvas');
      if (!canvas) return;
      const ctx = canvas.getContext('2d');
      let width = (canvas.width = window.innerWidth);
      let height = (canvas.height = window.innerHeight);

      const particles = [];
      const particleCount = Math.min(60, Math.floor((width * height) / 20000));
      const connectionDistance = 120;
      const mouse = { x: null, y: null, radius: 150 };

      for (let i = 0; i < particleCount; i++) {
        particles.push({
          x: Math.random() * width,
          y: Math.random() * height,
          vx: (Math.random() - 0.5) * 0.4,
          vy: (Math.random() - 0.5) * 0.4,
          radius: Math.random() * 2 + 1.2,
          colorIndex: Math.floor(Math.random() * 2)
        });
      }

      window.addEventListener('mousemove', (e) => {
        mouse.x = e.clientX;
        mouse.y = e.clientY;
      });
      window.addEventListener('mouseleave', () => {
        mouse.x = null;
        mouse.y = null;
      });
      window.addEventListener('resize', () => {
        width = canvas.width = window.innerWidth;
        height = canvas.height = window.innerHeight;
      });

      function draw() {
        ctx.clearRect(0, 0, width, height);
        const color1 = 'rgba(0, 240, 255, 0.4)';
        const color2 = 'rgba(167, 139, 250, 0.4)';
        const lineColor = 'rgba(0, 240, 255, 0.05)';

        particles.forEach((p) => {
          p.x += p.vx;
          p.y += p.vy;
          if (p.x < 0 || p.x > width) p.vx *= -1;
          if (p.y < 0 || p.y > height) p.vy *= -1;

          if (mouse.x !== null && mouse.y !== null) {
            const dx = mouse.x - p.x;
            const dy = mouse.y - p.y;
            const dist = Math.sqrt(dx * dx + dy * dy);
            if (dist < mouse.radius) {
              const force = (mouse.radius - dist) / mouse.radius;
              p.x += (dx / dist) * force * 0.4;
              p.y += (dy / dist) * force * 0.4;
            }
          }

          ctx.beginPath();
          ctx.arc(p.x, p.y, p.radius, 0, Math.PI * 2);
          ctx.fillStyle = p.colorIndex === 0 ? color1 : color2;
          ctx.fill();
        });

        for (let i = 0; i < particles.length; i++) {
          for (let j = i + 1; j < particles.length; j++) {
            const p1 = particles[i];
            const p2 = particles[j];
            const dx = p1.x - p2.x;
            const dy = p1.y - p2.y;
            const dist = Math.sqrt(dx * dx + dy * dy);

            if (dist < connectionDistance) {
              const alpha = (1 - dist / connectionDistance) * 0.85;
              ctx.beginPath();
              ctx.moveTo(p1.x, p1.y);
              ctx.lineTo(p2.x, p2.y);
              ctx.strokeStyle = `rgba(0, 240, 255, ${alpha * 0.05})`;
              ctx.lineWidth = 0.65;
              ctx.stroke();
            }
          }
        }
        requestAnimationFrame(draw);
      }
      draw();
    })();
  </script>
</body>
</html>
