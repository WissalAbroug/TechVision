</main>

<footer>
  <div class="container">
    <div class="grid grid-4" style="gap:2rem">
      <div>
        <div class="brand">⚡ <?= APP_NAME ?></div>
        <p style="color:rgba(255,255,255,.5);font-size:.875rem;margin-top:.5rem">
          <?= $lang === 'fr' ? 'La plateforme RH intelligente qui connecte talents et opportunités.' : 'The intelligent HR platform connecting talents and opportunities.' ?>
        </p>
      </div>
      <div>
        <h4 style="color:#fff;margin-bottom:1rem;font-size:.9rem"><?= $lang === 'fr' ? 'Candidats' : 'Candidates' ?></h4>
        <ul style="display:flex;flex-direction:column;gap:.5rem">
          <li><a href="<?= APP_URL ?>/?ctrl=offre&action=index" style="color:rgba(255,255,255,.5);font-size:.875rem"><?= __('nav.jobs') ?></a></li>
          <li><a href="<?= APP_URL ?>/?ctrl=cv&action=create" style="color:rgba(255,255,255,.5);font-size:.875rem"><?= __('cv.create') ?></a></li>
          <li><a href="<?= APP_URL ?>/?ctrl=formation&action=index" style="color:rgba(255,255,255,.5);font-size:.875rem"><?= __('nav.trainings') ?></a></li>
        </ul>
      </div>
      <div>
        <h4 style="color:#fff;margin-bottom:1rem;font-size:.9rem"><?= $lang === 'fr' ? 'Entreprises' : 'Companies' ?></h4>
        <ul style="display:flex;flex-direction:column;gap:.5rem">
          <li><a href="<?= APP_URL ?>/?ctrl=auth&action=register" style="color:rgba(255,255,255,.5);font-size:.875rem"><?= $lang === 'fr' ? 'Publier une offre' : 'Post a job' ?></a></li>
          <li><a href="<?= APP_URL ?>/?ctrl=societe&action=candidats" style="color:rgba(255,255,255,.5);font-size:.875rem"><?= $lang === 'fr' ? 'Gérer candidats' : 'Manage candidates' ?></a></li>
        </ul>
      </div>
      <div>
        <h4 style="color:#fff;margin-bottom:1rem;font-size:.9rem">Support</h4>
        <ul style="display:flex;flex-direction:column;gap:.5rem">
          <li><a href="<?= APP_URL ?>/?ctrl=reclamation&action=create" style="color:rgba(255,255,255,.5);font-size:.875rem"><?= __('recl.new') ?></a></li>
        </ul>
      </div>
    </div>
    <div class="footer-bottom flex-between" style="flex-wrap:wrap;gap:1rem">
      <span>© <?= date('Y') ?> <?= APP_NAME ?>. <?= $lang === 'fr' ? 'Tous droits réservés.' : 'All rights reserved.' ?></span>
      <span style="font-size:.8rem">Made with ❤️ — PHP MVC + PDO</span>
    </div>
  </div>
</footer>

<!-- ══════════════════════════════════════════════════════
     🤖 PATHFINDER AI ROBOT CHATBOT WIDGET
     ══════════════════════════════════════════════════════ -->
<div id="ai-robot-widget">
  <!-- Robot trigger button -->
  <div id="ai-robot-btn" title="<?= $lang==='fr'?'Parlez à PathBot IA':'Talk to PathBot AI' ?>">
    <img src="<?= APP_URL ?>/public/images/pathfinder robot.png" alt="PathBot AI" id="ai-robot-img">
    <div id="ai-robot-pulse"></div>
    <div id="ai-robot-badge">IA</div>
  </div>

  <!-- Chat Panel -->
  <div id="ai-chat-panel">
    <div id="ai-chat-header">
      <div style="display:flex;align-items:center;gap:.75rem">
        <img src="<?= APP_URL ?>/public/images/pathfinder robot.png" alt="PathBot" style="width:38px;height:38px;object-fit:contain;filter:drop-shadow(0 0 8px rgba(99,102,241,.8))">
        <div>
          <div style="font-weight:700;font-size:.95rem;color:#fff">PathBot <span style="color:#818cf8">IA</span></div>
          <div style="font-size:.72rem;color:#6ee7b7;display:flex;align-items:center;gap:.3rem">
            <span style="width:7px;height:7px;border-radius:50%;background:#6ee7b7;display:inline-block;animation:pulse-green 1.5s infinite"></span>
            <?= $lang==='fr'?'En ligne — Prêt à vous aider':'Online — Ready to help' ?>
          </div>
        </div>
      </div>
      <button id="ai-chat-close" aria-label="Fermer">✕</button>
    </div>

    <div id="ai-chat-messages">
      <div class="ai-msg bot">
        <div class="ai-bubble">
          <?= $lang==='fr'
            ? '👋 Bonjour ! Je suis <strong>PathBot</strong>, votre assistant IA carrière. Je peux vous aider à trouver des offres, préparer vos entretiens ou améliorer votre CV. Que puis-je faire pour vous ?'
            : '👋 Hello! I\'m <strong>PathBot</strong>, your AI career assistant. I can help you find jobs, prepare interviews, or improve your CV. How can I help?' ?>
        </div>
      </div>
    </div>

    <!-- Quick Suggestions -->
    <div id="ai-suggestions">
      <?php
      $suggestions = $lang === 'fr'
        ? ['🔍 Trouver des offres', '📄 Améliorer mon CV', '🎤 Préparer un entretien', '📚 Formations recommandées', '💼 Conseils carrière']
        : ['🔍 Find job offers', '📄 Improve my CV', '🎤 Interview prep', '📚 Recommended trainings', '💼 Career advice'];
      foreach ($suggestions as $s): ?>
      <button class="ai-suggestion-btn"><?= $s ?></button>
      <?php endforeach; ?>
    </div>

    <div id="ai-chat-input-wrap">
      <input type="text" id="ai-chat-input" placeholder="<?= $lang==='fr'?'Posez votre question...':'Ask me anything...' ?>" autocomplete="off">
      <button id="ai-send-btn" aria-label="Envoyer">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
      </button>
    </div>
  </div>
</div>

<style>
/* ── Robot Widget ── */
#ai-robot-widget { position:fixed; bottom:40px; right:40px; z-index:9999; }

#ai-robot-btn {
  position:relative; width:110px; height:110px; cursor:pointer;
  transition:transform .3s cubic-bezier(.34,1.56,.64,1);
  filter:drop-shadow(0 8px 24px rgba(99,102,241,.55));
}
#ai-robot-btn:hover { transform:scale(1.12) translateY(-4px); }
#ai-robot-img { width:100%; height:100%; object-fit:contain; }

#ai-robot-pulse {
  position:absolute; inset:-8px; border-radius:50%;
  background:radial-gradient(circle, rgba(99,102,241,.25) 0%, transparent 70%);
  animation:robot-pulse 2.5s ease-in-out infinite;
}
#ai-robot-badge {
  position:absolute; top:-4px; right:-4px;
  background:linear-gradient(135deg,#6366f1,#8b5cf6);
  color:#fff; font-size:.6rem; font-weight:800;
  padding:.2rem .4rem; border-radius:99px;
  letter-spacing:.05em; box-shadow:0 2px 8px rgba(99,102,241,.6);
  animation:badge-bounce 2s ease-in-out infinite;
}

/* Panel */
#ai-chat-panel {
  position:absolute; bottom:140px; right:0;
  width:420px; max-height:600px;
  background:rgba(13,11,38,.97);
  border:1px solid rgba(99,102,241,.35);
  border-radius:20px;
  box-shadow:0 24px 64px rgba(0,0,0,.7), 0 0 0 1px rgba(99,102,241,.15), inset 0 1px 0 rgba(255,255,255,.05);
  display:flex; flex-direction:column; overflow:hidden;
  transform:scale(.85) translateY(20px);
  transform-origin:bottom right;
  opacity:0; pointer-events:none;
  transition:all .35s cubic-bezier(.34,1.56,.64,1);
  backdrop-filter:blur(16px);
}
#ai-chat-panel.open {
  transform:scale(1) translateY(0);
  opacity:1; pointer-events:all;
}

#ai-chat-header {
  display:flex; align-items:center; justify-content:space-between;
  padding:.9rem 1.1rem;
  background:linear-gradient(135deg,rgba(99,102,241,.25),rgba(139,92,246,.2));
  border-bottom:1px solid rgba(99,102,241,.2);
  flex-shrink:0;
}
#ai-chat-close {
  background:rgba(255,255,255,.08); border:none; color:#fff;
  width:28px; height:28px; border-radius:50%; cursor:pointer;
  font-size:.85rem; display:flex; align-items:center; justify-content:center;
  transition:all .2s;
}
#ai-chat-close:hover { background:rgba(239,68,68,.2); color:#f87171; }

#ai-chat-messages {
  flex:1; overflow-y:auto; padding:1rem;
  display:flex; flex-direction:column; gap:.75rem;
  scrollbar-width:thin; scrollbar-color:rgba(99,102,241,.3) transparent;
}

.ai-msg { display:flex; }
.ai-msg.user { justify-content:flex-end; }
.ai-msg.bot  { justify-content:flex-start; }

.ai-bubble {
  max-width:78%; padding:.65rem .9rem;
  border-radius:14px; font-size:.84rem; line-height:1.55;
  animation:bubble-in .3s ease;
}
.ai-msg.bot  .ai-bubble { background:rgba(99,102,241,.18); color:#e2e8f0; border-bottom-left-radius:4px; border:1px solid rgba(99,102,241,.2); }
.ai-msg.user .ai-bubble { background:linear-gradient(135deg,#6366f1,#8b5cf6); color:#fff; border-bottom-right-radius:4px; }

.ai-typing .ai-bubble { color:#818cf8; }
.ai-typing-dots { display:inline-flex; gap:3px; }
.ai-typing-dots span { width:6px; height:6px; border-radius:50%; background:#818cf8; animation:typing-dot .9s infinite; }
.ai-typing-dots span:nth-child(2) { animation-delay:.15s; }
.ai-typing-dots span:nth-child(3) { animation-delay:.3s; }

/* Suggestions */
#ai-suggestions {
  padding:.5rem .75rem .25rem;
  display:flex; flex-wrap:wrap; gap:.4rem; flex-shrink:0;
  border-top:1px solid rgba(99,102,241,.12);
}
.ai-suggestion-btn {
  background:rgba(99,102,241,.12); border:1px solid rgba(99,102,241,.25);
  color:#a5b4fc; font-size:.72rem; padding:.3rem .65rem;
  border-radius:99px; cursor:pointer; transition:all .2s;
  font-family:inherit;
}
.ai-suggestion-btn:hover { background:rgba(99,102,241,.3); color:#fff; transform:translateY(-1px); }

/* Input */
#ai-chat-input-wrap {
  display:flex; gap:.5rem; padding:.75rem .9rem;
  border-top:1px solid rgba(99,102,241,.2); flex-shrink:0;
  background:rgba(0,0,0,.2);
}
#ai-chat-input {
  flex:1; background:rgba(99,102,241,.1); border:1px solid rgba(99,102,241,.25);
  border-radius:99px; padding:.5rem 1rem; color:#e2e8f0;
  font-size:.83rem; outline:none; font-family:inherit;
  transition:border-color .2s;
}
#ai-chat-input:focus { border-color:rgba(99,102,241,.6); }
#ai-chat-input::placeholder { color:#4b5563; }
#ai-send-btn {
  width:36px; height:36px; border-radius:50%; flex-shrink:0;
  background:linear-gradient(135deg,#6366f1,#8b5cf6);
  border:none; color:#fff; cursor:pointer;
  display:flex; align-items:center; justify-content:center;
  transition:all .2s; box-shadow:0 2px 8px rgba(99,102,241,.4);
}
#ai-send-btn:hover { transform:scale(1.1); box-shadow:0 4px 16px rgba(99,102,241,.6); }

/* Keyframes */
@keyframes robot-pulse { 0%,100%{transform:scale(1);opacity:.6} 50%{transform:scale(1.2);opacity:1} }
@keyframes badge-bounce { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-3px)} }
@keyframes bubble-in { from{transform:scale(.8) translateY(8px);opacity:0} to{transform:scale(1) translateY(0);opacity:1} }
@keyframes typing-dot { 0%,60%,100%{transform:translateY(0)} 30%{transform:translateY(-5px)} }
@keyframes pulse-green { 0%,100%{opacity:1} 50%{opacity:.4} }

@media(max-width:480px) {
  #ai-chat-panel { width:calc(100vw - 32px); right:-14px; }
}

/* ── Lang Toggle Switch ─────────────────────────── */
.lang-switch {
  display: flex;
  align-items: center;
  gap: .45rem;
  cursor: pointer;
  user-select: none;
  padding: .3rem .5rem;
  border-radius: 99px;
  transition: background .2s;
}
.lang-switch:hover { background: var(--primary-50); }
.lang-label {
  font-size: .78rem;
  font-weight: 800;
  color: var(--text-muted);
  letter-spacing: .05em;
  transition: color .25s;
  font-family: 'Outfit', sans-serif;
  min-width: 20px;
  text-align: center;
}
.lang-label.lang-active { color: var(--primary); }
.lang-track {
  width: 44px;
  height: 24px;
  border-radius: 99px;
  background: var(--border);
  border: 1.5px solid var(--border);
  position: relative;
  transition: background .25s;
  flex-shrink: 0;
}
body.theme-light .lang-track { background: #e2e8f0; border-color: #cbd5e1; }
.lang-thumb {
  position: absolute;
  left: 3px;
  top: 50%;
  transform: translateY(-50%);
  width: 16px;
  height: 16px;
  border-radius: 50%;
  background: linear-gradient(135deg, var(--primary), var(--purple));
  transition: left .25s cubic-bezier(.34,1.56,.64,1), background .25s;
  box-shadow: 0 2px 6px rgba(0,0,0,.3);
}
.lang-thumb.lang-thumb-right { left: calc(100% - 19px); }
body.theme-light .lang-thumb { box-shadow: 0 2px 6px rgba(37,99,235,.3); }

/* ── Notification System ───────────────────────── */
.notif-wrap {
  position: relative;
}
.notif-btn {
  background: none;
  border: none;
  cursor: pointer;
  color: var(--text-secondary);
  padding: .45rem;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  position: relative;
  transition: all .2s;
}
.notif-btn:hover { background: var(--primary-50); color: var(--primary); }
.notif-badge {
  position: absolute;
  top: 2px; right: 2px;
  min-width: 16px; height: 16px;
  background: var(--danger);
  color: #fff;
  font-size: .6rem; font-weight: 800;
  border-radius: 99px;
  display: flex; align-items: center; justify-content: center;
  padding: 0 3px;
  line-height: 1;
  animation: badge-bounce 2s ease-in-out infinite;
  border: 2px solid var(--bg);
}
.notif-dropdown {
  position: absolute;
  top: calc(100% + 10px);
  right: -10px;
  width: 340px;
  background: var(--bg-card);
  border: 1px solid var(--border);
  border-radius: var(--radius);
  box-shadow: var(--shadow-lg);
  z-index: 300;
  opacity: 0;
  pointer-events: none;
  transform: translateY(-8px) scale(.97);
  transition: all .25s cubic-bezier(.34,1.56,.64,1);
  overflow: hidden;
}
.notif-dropdown.open {
  opacity: 1;
  pointer-events: all;
  transform: translateY(0) scale(1);
}
.notif-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: .85rem 1.1rem;
  border-bottom: 1px solid var(--border);
  font-weight: 700;
  font-size: .875rem;
  color: var(--text-primary);
}
.notif-clear {
  background: none;
  border: none;
  cursor: pointer;
  font-size: .75rem;
  color: var(--primary);
  font-weight: 600;
  font-family: inherit;
  padding: .2rem .5rem;
  border-radius: 4px;
  transition: all .2s;
}
.notif-clear:hover { background: var(--primary-50); }
.notif-list {
  max-height: 320px;
  overflow-y: auto;
  scrollbar-width: thin;
}
.notif-item {
  display: flex;
  align-items: flex-start;
  gap: .75rem;
  padding: .85rem 1.1rem;
  border-bottom: 1px solid var(--border);
  transition: background .2s;
  cursor: pointer;
  position: relative;
}
.notif-item:hover { background: var(--primary-50); }
.notif-item.unread::before {
  content: '';
  position: absolute;
  left: .6rem;
  top: 50%;
  transform: translateY(-50%);
  width: 7px; height: 7px;
  border-radius: 50%;
  background: var(--primary);
  box-shadow: 0 0 6px var(--primary);
}
.notif-item.unread { padding-left: 1.6rem; }
.notif-icon {
  width: 36px; height: 36px;
  border-radius: 50%;
  display: flex; align-items: center; justify-content: center;
  font-size: 1rem;
  flex-shrink: 0;
  background: var(--primary-100);
}
.notif-content { flex: 1; min-width: 0; }
.notif-title { font-size: .82rem; font-weight: 600; color: var(--text-primary); margin-bottom: .15rem; }
.notif-time  { font-size: .72rem; color: var(--text-muted); }
.notif-empty {
  text-align: center;
  padding: 2.5rem 1rem;
  color: var(--text-muted);
  font-size: .875rem;
}
</style>

<script>
(function() {
  const btn    = document.getElementById('ai-robot-btn');
  const panel  = document.getElementById('ai-chat-panel');
  const close  = document.getElementById('ai-chat-close');
  const input  = document.getElementById('ai-chat-input');
  const sendBtn= document.getElementById('ai-send-btn');
  const msgs   = document.getElementById('ai-chat-messages');
  const suggs  = document.querySelectorAll('.ai-suggestion-btn');
  const lang   = '<?= $lang ?>';

  /* ── Knowledge Base ─────────────────────────────────── */
  const KB = {
    fr: {
      "cv":         "📄 **Pour un CV percutant**, assurez-vous d'inclure :\n• Un titre professionnel clair\n• Vos compétences clés (max 8-10)\n• Des expériences avec des résultats chiffrés\n• Des formations certifiantes\n\n🔗 Vous pouvez créer votre CV directement sur PathFinder AI via **Mon CV**.",
      "entretien":  "🎤 **Préparer votre entretien** :\n• Renseignez-vous sur l'entreprise (site, LinkedIn)\n• Préparez la méthode **STAR** (Situation, Tâche, Action, Résultat)\n• Anticipez : 'Parlez-moi de vous', 'Pourquoi nous ?'\n• Posez 2-3 questions à la fin\n\n💡 PathFinder IA propose des simulations d'entretien dans votre espace candidat !",
      "offre":      "🔍 **Trouver des offres** :\n• Consultez notre page **Offres d'emploi** pour voir toutes les opportunités\n• Utilisez les filtres (type de contrat, localisation)\n• Votre score de matching IA vous indique votre compatibilité\n• Sauvegardez vos offres favorites avec ❤️",
      "formation":  "📚 **Formations recommandées** :\n• Accédez à notre catalogue de formations dans **Formations**\n• Elles sont triées par niveau (débutant → expert)\n• Gratuites et payantes disponibles\n• Elles boostent votre score de matching sur les offres !",
      "conseil":    "💼 **Conseils carrière** :\n• **Réseau** : 70% des emplois se trouvent via le réseau\n• **LinkedIn** : Mettez à jour votre profil chaque mois\n• **Candidatures ciblées** : Mieux vaut 5 dossiers soignés que 50 génériques\n• **Suivi** : Relancez après 1 semaine si pas de réponse\n\nSur PathFinder AI, suivez toutes vos candidatures en temps réel 📊",
      "salaire":    "💰 **Négocier son salaire** :\n• Renseignez-vous sur les grilles du marché (Glassdoor, LinkedIn Salaries)\n• Attendez qu'on vous pose la question ou la fin du 2e entretien\n• Proposez une fourchette, pas un fixe\n• Valorisez vos compétences rares\n• N'oubliez pas les avantages (télétravail, tickets resto, mutuelle)",
      "default":    "🤖 Je suis PathBot, votre assistant carrière ! Je peux vous aider sur :\n• 🔍 **Offres d'emploi** et matching IA\n• 📄 **Créer/améliorer votre CV**\n• 🎤 **Préparer vos entretiens**\n• 📚 **Formations** recommandées\n• 💼 **Conseils carrière** et salaire\n\nQue souhaitez-vous explorer ?"
    },
    en: {
      "cv":         "📄 **For a standout CV**:\n• Clear professional title\n• Key skills (max 8-10)\n• Experience with measurable results\n• Certifications\n\n🔗 Create your CV directly on PathFinder AI via **My CV**.",
      "entretien":  "🎤 **Interview prep tips**:\n• Research the company (website, LinkedIn)\n• Use the **STAR** method (Situation, Task, Action, Result)\n• Prepare for: 'Tell me about yourself', 'Why us?'\n• Ask 2-3 questions at the end\n\n💡 PathFinder AI offers interview simulations in your candidate space!",
      "offre":      "🔍 **Find job offers**:\n• Visit our **Job Offers** page for all opportunities\n• Use filters (contract type, location)\n• Your AI matching score shows compatibility\n• Save your favorite offers with ❤️",
      "formation":  "📚 **Recommended trainings**:\n• Access our training catalog in **Trainings**\n• Sorted by level (beginner → expert)\n• Free and paid options available\n• They boost your matching score on job offers!",
      "conseil":    "💼 **Career advice**:\n• **Network**: 70% of jobs are found through networking\n• **LinkedIn**: Update your profile monthly\n• **Targeted applications**: 5 quality applications beat 50 generic ones\n• **Follow up**: Send a follow-up after 1 week with no response\n\nTrack all your applications in real-time on PathFinder AI 📊",
      "salaire":    "💰 **Salary negotiation**:\n• Research market rates (Glassdoor, LinkedIn Salaries)\n• Wait until they ask or the 2nd interview\n• Give a range, not a fixed number\n• Highlight rare skills\n• Don't forget perks (remote work, meal vouchers, health insurance)",
      "default":    "🤖 I'm PathBot, your career AI assistant! I can help with:\n• 🔍 **Job offers** and AI matching\n• 📄 **Create/improve your CV**\n• 🎤 **Interview preparation**\n• 📚 **Recommended trainings**\n• 💼 **Career advice** and salary\n\nWhat would you like to explore?"
    }
  };

  function getResponse(text) {
    const t = text.toLowerCase();
    const kb = KB[lang] || KB.fr;
    if (t.includes('cv') || t.includes('curriculum') || t.includes('resume')) return kb.cv;
    if (t.includes('entretien') || t.includes('interview') || t.includes('prépare') || t.includes('prep')) return kb.entretien;
    if (t.includes('offre') || t.includes('job') || t.includes('emploi') || t.includes('poste') || t.includes('find') || t.includes('trouver')) return kb.offre;
    if (t.includes('formation') || t.includes('train') || t.includes('cours') || t.includes('apprendr')) return kb.formation;
    if (t.includes('salaire') || t.includes('salary') || t.includes('rémunér') || t.includes('pay') || t.includes('négoci')) return kb.salaire;
    if (t.includes('conseil') || t.includes('advice') || t.includes('carrière') || t.includes('career') || t.includes('astuce')) return kb.conseil;
    if (t.includes('favoris') || t.includes('favorite') || t.includes('sauvegarder') || t.includes('save')) {
      return lang === 'fr'
        ? '❤️ **Système de favoris** : Cliquez sur le bouton **🤍** sur n\'importe quelle offre pour la sauvegarder. Retrouvez toutes vos offres favorites dans **❤️ Favoris** dans la navigation !'
        : '❤️ **Favorites system**: Click the **🤍** button on any job offer to save it. Find all your saved offers in **❤️ Favorites** in the navigation!';
    }
    if (t.includes('bonjour') || t.includes('hello') || t.includes('salut') || t.includes('hi')) {
      return lang === 'fr'
        ? '👋 Bonjour ! Je suis PathBot, prêt à booster votre carrière ! Comment puis-je vous aider aujourd\'hui ?'
        : '👋 Hello! I\'m PathBot, ready to boost your career! How can I help you today?';
    }
    return kb.default;
  }

  function addMessage(text, type) {
    const div = document.createElement('div');
    div.className = 'ai-msg ' + type;
    const bubble = document.createElement('div');
    bubble.className = 'ai-bubble';
    // Convert **bold** markdown
    bubble.innerHTML = text.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>').replace(/\n/g, '<br>');
    div.appendChild(bubble);
    msgs.appendChild(div);
    msgs.scrollTop = msgs.scrollHeight;
    return div;
  }

  function showTyping() {
    const div = document.createElement('div');
    div.className = 'ai-msg bot ai-typing';
    div.innerHTML = '<div class="ai-bubble"><div class="ai-typing-dots"><span></span><span></span><span></span></div></div>';
    msgs.appendChild(div);
    msgs.scrollTop = msgs.scrollHeight;
    return div;
  }

  async function sendMessage(text) {
    if (!text.trim()) return;
    addMessage(text, 'user');
    input.value = '';

    const typing = showTyping();
    await new Promise(r => setTimeout(r, 900 + Math.random() * 600));
    typing.remove();

    const response = getResponse(text);
    addMessage(response, 'bot');
  }

  /* ── Event Listeners ─── */
  btn.addEventListener('click', () => panel.classList.toggle('open'));
  close.addEventListener('click', () => panel.classList.remove('open'));

  sendBtn.addEventListener('click', () => sendMessage(input.value));
  input.addEventListener('keydown', e => { if (e.key === 'Enter') sendMessage(input.value); });

  suggs.forEach(s => s.addEventListener('click', () => {
    sendMessage(s.textContent);
    document.getElementById('ai-suggestions').style.display = 'none';
  }));

  // Close on outside click
  document.addEventListener('click', e => {
    if (!document.getElementById('ai-robot-widget').contains(e.target)) {
      panel.classList.remove('open');
    }
  });
})();
</script>

<script>
/* ── Notification System ─────────────────────────────── */
(function() {
  const btn      = document.getElementById('notifBtn');
  const dropdown = document.getElementById('notifDropdown');
  const badge    = document.getElementById('notifBadge');
  const list     = document.getElementById('notifList');
  const clearBtn = document.getElementById('notifClear');
  if (!btn) return; // not logged in

  const lang = '<?= $lang ?>';
  const STORAGE_KEY = 'pf_notifications_read';

  // Sample notifications — in a real app these come from the server
  const sampleNotifs = [
    <?php if (isLoggedIn() && hasRole('candidat')): ?>
    { id:'n1', icon:'💼', title: lang==='fr' ? 'Nouvelle offre correspondant à votre profil' : 'New offer matching your profile', time: lang==='fr' ? 'Il y a 5 min' : '5 min ago', link: '<?= APP_URL ?>/?ctrl=offre&action=index' },
    { id:'n2', icon:'📬', title: lang==='fr' ? 'Votre candidature a été vue' : 'Your application has been viewed', time: lang==='fr' ? 'Il y a 1h' : '1h ago', link: '<?= APP_URL ?>/?ctrl=candidature&action=index' },
    { id:'n3', icon:'🎤', title: lang==='fr' ? 'Entretien planifié' : 'Interview scheduled', time: lang==='fr' ? 'Hier' : 'Yesterday', link: '<?= APP_URL ?>/?ctrl=entretien&action=index' },
    <?php elseif (isLoggedIn() && hasRole('societe')): ?>
    { id:'n1', icon:'👤', title: lang==='fr' ? 'Nouveau candidat a postulé' : 'New candidate applied', time: lang==='fr' ? 'Il y a 10 min' : '10 min ago', link: '<?= APP_URL ?>/?ctrl=societe&action=candidats' },
    { id:'n2', icon:'📋', title: lang==='fr' ? 'Entretien à confirmer' : 'Interview to confirm', time: lang==='fr' ? 'Il y a 2h' : '2h ago', link: '<?= APP_URL ?>/?ctrl=entretien&action=societeIndex' },
    <?php endif; ?>
  ];

  let readIds = JSON.parse(localStorage.getItem(STORAGE_KEY) || '[]');

  function renderNotifs() {
    if (!sampleNotifs.length) return;
    list.innerHTML = '';
    let unreadCount = 0;
    sampleNotifs.forEach(n => {
      const isUnread = !readIds.includes(n.id);
      if (isUnread) unreadCount++;
      const item = document.createElement('div');
      item.className = 'notif-item' + (isUnread ? ' unread' : '');
      item.innerHTML = `
        <div class="notif-icon">${n.icon}</div>
        <div class="notif-content">
          <div class="notif-title">${n.title}</div>
          <div class="notif-time">${n.time}</div>
        </div>`;
      item.addEventListener('click', () => {
        if (!readIds.includes(n.id)) {
          readIds.push(n.id);
          localStorage.setItem(STORAGE_KEY, JSON.stringify(readIds));
        }
        if (n.link) window.location.href = n.link;
      });
      list.appendChild(item);
    });
    if (unreadCount > 0) {
      badge.textContent = unreadCount;
      badge.style.display = 'flex';
    } else {
      badge.style.display = 'none';
    }
  }

  renderNotifs();

  btn.addEventListener('click', (e) => {
    e.stopPropagation();
    dropdown.classList.toggle('open');
  });

  if (clearBtn) {
    clearBtn.addEventListener('click', (e) => {
      e.stopPropagation();
      readIds = sampleNotifs.map(n => n.id);
      localStorage.setItem(STORAGE_KEY, JSON.stringify(readIds));
      renderNotifs();
    });
  }

  document.addEventListener('click', (e) => {
    if (!document.getElementById('notifWrap')?.contains(e.target)) {
      dropdown.classList.remove('open');
    }
  });
})();
</script>
<script src="<?= APP_URL ?>/public/js/main.js"></script>

<?php if (isset($extraJs)): echo $extraJs; endif; ?>
</body>
</html>

