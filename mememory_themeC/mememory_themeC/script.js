// script.js - handles flip, audio, timer, AJAX
document.addEventListener('DOMContentLoaded', () => {
  const board = document.getElementById('board');
  if (!board) return;
  const cards = Array.from(document.querySelectorAll('.card'));
  const flipSound = document.getElementById('flipSound');
  const matchSound = document.getElementById('matchSound');
  const wrongSound = document.getElementById('wrongSound');
  const winSound = document.getElementById('winSound');
  const timePerTurn = parseInt(board.dataset.time || 15, 10);

  let first = null, second = null;
  let lock = false;
  let current = document.querySelector('.player.active') ? Array.from(document.querySelectorAll('.player')).indexOf(document.querySelector('.player.active')) : 0;
  let timerIntervals = [null, null];
  let times = [parseInt(document.getElementById('time0').innerText), parseInt(document.getElementById('time1').innerText)];

  startTimer(current);

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
    flipSound && flipSound.play().catch(()=>{});
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
        matchSound && matchSound.play().catch(()=>{});
        first.classList.add('matched'); second.classList.add('matched');
        document.getElementById('score0').innerText = data.scores[0];
        document.getElementById('score1').innerText = data.scores[1];
        first = null; second = null; lock=false;
        if (data.finished) { winSound && winSound.play().catch(()=>{}); setTimeout(()=> location.href='result.php', 600); }
      } else {
        wrongSound && wrongSound.play().catch(()=>{});
        setTimeout(()=>{
          first.classList.remove('flipped'); second.classList.remove('flipped');
          first = null; second = null; lock=false;
          if (data.current !== undefined) switchActive(data.current);
          document.getElementById('score0').innerText = data.scores[0];
          document.getElementById('score1').innerText = data.scores[1];
        }, 700);
      }
    }).catch(()=>{
      setTimeout(()=>{ first.classList.remove('flipped'); second.classList.remove('flipped'); first=null; second=null; lock=false; },700);
    });
  }));

  document.getElementById('resetBtn') && document.getElementById('resetBtn').addEventListener('click', ()=> {
    if (confirm('Restart game?')) location.href='index.php';
  });
});
