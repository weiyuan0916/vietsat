const fs = require('fs');
const path = require('path');
const file = path.join(__dirname, 'background.js');
const s = fs.readFileSync(file, 'utf8').split('\\n');
const { spawnSync } = require('child_process');
let low = 1, high = s.length, firstFail = null;
while (low <= high) {
  const mid = Math.floor((low + high) / 2);
  const chunk = s.slice(0, mid).join('\\n') + '\\n';
  fs.writeFileSync('/tmp/bg_chunk.js', chunk);
  const res = spawnSync('node', ['--check', '/tmp/bg_chunk.js'], { encoding: 'utf8' });
  if (res.status === 0) {
    // prefix parses
    low = mid + 1;
  } else {
    firstFail = mid;
    high = mid - 1;
  }
}
if (firstFail === null) {
  console.log('No failing prefix found; whole file parses?');
} else {
  console.log('first failing line:', firstFail);
  console.log('Context (firstFail-6 .. firstFail+6):\\n');
  const a = Math.max(1, firstFail - 6);
  const b = Math.min(s.length, firstFail + 6);
  for (let i = a; i <= b; i++) {
    console.log(i + ': ' + s[i-1]);
  }
}



