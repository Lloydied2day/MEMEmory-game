// script.js - handles flip, audio, timer, AJAX
document.addEventListener('DOMContentLoaded', () => {
  const board = document.getElementById('board');
  if (!board) return;
  const cards = Array.from(document.querySelectorAll('.card'));
  const bgSound = document.getElementById('bgSound');
  const flipSound = document.getElementById('flipSound');
  const matchSound = document.getElementById('matchSound');
  const wrongSound = document.getElementById('wrongSound');
  const winSound = document.getElementById('winSound');
  const audioEls = [bgSound, flipSound, matchSound, wrongSound, winSound].filter(Boolean);
  const gameOverSplash = document.getElementById('gameOverSplash');
  const timePerTurn = parseInt(board.dataset.time || 15, 10);

  let first = null, second = null;
  let lock = false;
  let current = document.querySelector('.player.active') ? Array.from(document.querySelectorAll('.player')).indexOf(document.querySelector('.player.active')) : 0;
  let timerIntervals = [null, null];
  let times = [parseInt(document.getElementById('time0').innerText), parseInt(document.getElementById('time1').innerText)];

  document.addEventListener('pointerdown', unlockAudio, { once: true });
  attemptAutoPlayBg();
  startTimer(current);

  function unlockAudio() {
    audioEls.forEach(audio => {
      if (audio.dataset.unlocked) return;
      audio.dataset.unlocked = '1';
      audio.muted = true;
      const playPromise = audio.play();
      Promise.resolve(playPromise).then(() => {
        audio.pause();
        audio.currentTime = 0;
        audio.muted = false;
      }).catch(() => {});
    });
  }

  function startTimer(playerIdx) {
    clearInterval(timerIntervals[playerIdx]);
    times[playerIdx] = timePerTurn;
    document.getElementById('time' + playerIdx).innerText = times[playerIdx];
    timerIntervals[playerIdx] = setInterval(() => {
      times[playerIdx]--;
      document.getElementById('time' + playerIdx).innerText = times[playerIdx];
      if (times[playerIdx] <= 0) {
        clearInterval(timerIntervals[playerIdx]);
        fetch('action.php', {method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body:'action=switch'})
          .then(r=>r.json()).then(data=>{
            if (data.current !== undefined) { switchActive(data.current); }
          });
      }
    }, 1000);
  }

  function ensureBgMusic() {
    if (!bgSound) return;
    if (!bgSound.dataset.started) {
      bgSound.dataset.started = '1';
      bgSound.volume = 0.35;
    }
    if (bgSound.paused) {
      bgSound.play().catch(()=>{});
    }
  }

  function attemptAutoPlayBg() {
    if (!bgSound) return;
    bgSound.volume = 0.32;
    const promise = bgSound.play();
    if (promise && typeof promise.then === 'function') {
      promise.then(() => {
        bgSound.dataset.started = '1';
      }).catch(() => {
        // will retry on user interaction
      });
    }
  }

  function playSound(el) {
    if (!el) return;
    try {
      el.currentTime = 0;
      el.play().catch(()=>{});
    } catch (_) {}
  }

  function switchActive(idx) {
    document.querySelectorAll('.player').forEach((el,i)=>{
      el.classList.toggle('active', i===idx);
    });
    clearInterval(timerIntervals[0]); clearInterval(timerIntervals[1]);
    current = idx;
    startTimer(current);
  }

  cards.forEach(c => c.addEventListener('click', () => {
    if (lock || c.classList.contains('flipped') || c.classList.contains('matched')) return;
    ensureBgMusic();
    playSound(flipSound);
    c.classList.add('flipped');
    if (!first) { first = c; return; }
    second = c;
    lock = true;

    fetch('action.php', {
      method:'POST',
      headers:{'Content-Type':'application/x-www-form-urlencoded'},
      body:`action=match&a=${first.dataset.index}&b=${second.dataset.index}`
    }).then(r=>r.json()).then(data=>{
      if (data.result === 'match') {
        playSound(matchSound);
        setTimeout(()=>{
          first.classList.add('matched'); second.classList.add('matched');
          document.getElementById('score0').innerText = data.scores[0];
          document.getElementById('score1').innerText = data.scores[1];
          first = null; second = null; lock=false;
          if (data.finished) {
            playSound(winSound);
            triggerGameOverSplash();
          }
        }, 900);
      } else {
        playSound(wrongSound);
        first.classList.add('mismatch'); second.classList.add('mismatch');
        const firstCard = first, secondCard = second;
        setTimeout(()=>{
          firstCard.classList.remove('mismatch','flipped');
          secondCard.classList.remove('mismatch','flipped');
          first = null; second = null; lock=false;
          if (data.current !== undefined) switchActive(data.current);
          document.getElementById('score0').innerText = data.scores[0];
          document.getElementById('score1').innerText = data.scores[1];
        }, 900);
      }
    }).catch(()=>{
      setTimeout(()=>{
        if (first) first.classList.remove('flipped','mismatch');
        if (second) second.classList.remove('flipped','mismatch');
        first=null; second=null; lock=false;
      },700);
    });
  }));

  document.getElementById('resetBtn') && document.getElementById('resetBtn').addEventListener('click', ()=> {
    if (!confirm('Restart game?')) return;
    const go = () => { window.location.href='index.php'; };
    if (window.pageFadeLeave) window.pageFadeLeave(go); else go();
  });

  function triggerGameOverSplash() {
    if (gameOverSplash) {
      gameOverSplash.classList.add('show');
      gameOverSplash.addEventListener('animationend', () => {
        gameOverSplash.classList.remove('show');
        if (window.pageFadeLeave) {
          window.pageFadeLeave(() => { window.location.href='result.php'; });
        } else {
          window.location.href='result.php';
        }
      }, { once: true });
    } else {
      if (window.pageFadeLeave) {
        window.pageFadeLeave(() => { window.location.href='result.php'; });
      } else {
        setTimeout(()=> location.href='result.php', 2000);
      }
    }
  }
});
