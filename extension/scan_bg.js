const fs = require('fs');
const path = require('path');
const target = path.join(__dirname, 'background.js');
const s = fs.readFileSync(target, 'utf8');
let state = null; // null | 'single' | 'double' | 'backtick' | 'linecomment' | 'blockcomment' | 'regex'
let paren = 0, brace = 0, bracket = 0, line = 1;
function prevNonSpace(str, idx){ for(let k=idx-1;k>=0;k--){ const ch=str[k]; if(ch!==' '&&ch!=='\\t'&&ch!=='\\r'&&ch!=='\\n') return ch; } return null; }
for(let i=0;i<s.length;i++){
  const ch = s[i]; const nxt = s[i+1];
  if(state==='single'){
    if(ch==='\\\\') { i++; continue; }
    if(ch==="'") { state=null; continue; }
    if(ch==='\\n') line++;
    continue;
  }
  if(state==='double'){
    if(ch==='\\\\') { i++; continue; }
    if(ch==='"') { state=null; continue; }
    if(ch==='\\n') line++;
    continue;
  }
  if(state==='backtick'){
    if(ch==='\\\\') { i++; continue; }
    if(ch==='`') { state=null; continue; }
    if(ch==='\\n') line++;
    continue;
  }
  if(state==='linecomment'){
    if(ch==='\\n'){ state=null; line++; }
    continue;
  }
  if(state==='blockcomment'){
    if(ch==='*' && nxt==='/'){ state=null; i++; continue; }
    if(ch==='\\n') line++;
    continue;
  }
  if(state==='regex'){
    if(ch==='\\\\'){ i++; continue; }
    if(ch==='/' ){ state=null; continue; }
    if(ch==='\\n') line++;
    continue;
  }
  // not in any string/comment/regex
  if(ch==="'") { state='single'; continue; }
  if(ch==='"') { state='double'; continue; }
  if(ch==='`') { state='backtick'; continue; }
  if(ch==='/' && nxt==='/' ) { state='linecomment'; i++; continue; }
  if(ch==='/' && nxt==='*' ) { state='blockcomment'; i++; continue; }
  const prev = prevNonSpace(s, i);
  if(ch==='/' && nxt!=='/' && nxt!=='*' && (prev===null || '([{=,:;!&|?+-~<>'.includes(prev))){ state='regex'; continue; }
  if(ch==='(') paren++;
  else if(ch===')'){ paren--; if(paren<0){ console.log('Negative paren at line',line,'index',i); console.log('\\nContext:\\n'+s.slice(Math.max(0,i-120), Math.min(s.length,i+120))); process.exit(0);} }
  if(ch==='[') bracket++; else if(ch===']'){ bracket--; if(bracket<0){ console.log('Negative bracket at line',line,'index',i); process.exit(0);} }
  if(ch==='{') brace++; else if(ch==='}'){ brace--; if(brace<0){ console.log('Negative brace at line',line,'index',i); console.log('\\nContext:\\n'+s.slice(Math.max(0,i-120), Math.min(s.length,i+120))); process.exit(0);} }
  if(ch==='\\n') line++;
}
console.log('Finished scan. paren=',paren, 'brace=', brace, 'bracket=', bracket);



