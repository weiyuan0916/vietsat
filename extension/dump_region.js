const fs = require('fs');
const path = require('path');
const file = path.join(__dirname, 'background.js');
const s = fs.readFileSync(file);
const text = s.toString('utf8');
const lines = text.split('\\n');
const start = 452, end = 482;
console.log('Showing lines', start, 'to', end);
for (let i = start; i <= end && i <= lines.length; i++) {
  const ln = lines[i - 1];
  const bytes = Buffer.from(ln, 'utf8');
  const hex = Array.from(bytes).map(b => b.toString(16).padStart(2, '0')).join(' ');
  console.log(i + ':', ln);
  console.log('HEX:', hex);
}

