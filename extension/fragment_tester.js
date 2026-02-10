const fs = require('fs');
const path = require('path');
const file = path.join(__dirname, 'background.js');
const s = fs.readFileSync(file, 'utf8').split('\\n');

// handler region approximated from original analysis
const handlerStartLine = 178; // inclusive
const handlerEndLine = 479; // inclusive

// build base prefix (lines before handler)
const prefix = s.slice(0, handlerStartLine - 1).join('\\n') + '\\n';
// suffix to close out the rest of the file after handler
const suffix = s.slice(handlerEndLine).join('\\n') + '\\n';

const { spawnSync } = require('child_process');

for (let len = 1; len <= (handlerEndLine - handlerStartLine + 1); len++) {
  const frag = s.slice(handlerStartLine - 1, handlerStartLine - 1 + len).join('\\n') + '\\n';
  const combined = prefix + frag + suffix;
  fs.writeFileSync('/tmp/bg_test.js', combined);
  const res = spawnSync('node', ['--check', '/tmp/bg_test.js'], { encoding: 'utf8' });
  if (res.status !== 0) {
    console.log('Failure at handler fragment length', len, 'line', handlerStartLine + len - 1);
    console.log('Error output:\\n', res.stderr || res.stdout);
    // show context lines around failure
    const ctxStart = Math.max(handlerStartLine - 3, handlerStartLine - 3);
    const ctxEnd = Math.min(handlerStartLine - 1 + len + 3, handlerEndLine);
    for (let i = Math.max(handlerStartLine - 3, 1); i <= Math.min(handlerEndLine + 3, s.length); i++) {
      const mark = (i >= handlerStartLine && i <= handlerStartLine - 1 + len) ? '>' : ' ';
      console.log(`${mark}${i}: ${s[i-1]}`);
    }
    process.exit(0);
  }
}
console.log('No failure found within handler region when placed into prefix+suffix (unexpected).');



