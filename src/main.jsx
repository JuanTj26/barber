import React, { useEffect, useRef } from 'react';
import { createRoot } from 'react-dom/client';
import Lenis from 'lenis';
import gsap from 'gsap';
import './styles.css';
import './hero.css';
import './minimal.css';
import './photos.css';
import './cinematic.css';
import './casa.css';
import './pure-minimal.css';
import './responsive.css';
import './motion.css';
import './casa-motion.css';

const artColors = {
  arco: ['#22140c', '#e0903d', '#080403'],
  espejo: ['#1c1410', '#d9b080', '#070504'],
  poste: ['#14262a', '#b3261e', '#080b0b'],
  navaja: ['#3b2314', '#e6b676', '#070504'],
  fachada: ['#2a1610', '#7b1d18', '#0c0705'],
};

const photoSources = {
  arco: 'https://images.unsplash.com/photo-1585747860715-2ba37e788b70?auto=format&fit=crop&w=900&q=85',
  espejo: 'https://images.unsplash.com/photo-1781455793310-8427c96454c7?auto=format&fit=crop&w=900&q=85',
  poste: 'https://images.unsplash.com/photo-1778347855945-fb113ac3b00e?auto=format&fit=crop&w=900&q=85',
  navaja: 'https://images.unsplash.com/photo-1621605815971-fbc98d665033?auto=format&fit=crop&w=900&q=85',
  fachada: 'https://images.unsplash.com/photo-1675599193990-33d71150902b?auto=format&fit=crop&w=1200&q=85',
};

function Art({ type }) {
  const [dark, accent, black] = artColors[type];
  return (
    <svg viewBox="0 0 800 600" preserveAspectRatio="xMidYMid slice" aria-hidden="true">
      <defs>
        <linearGradient id={`bg-${type}`} x1="0" y1="0" x2="1" y2="1"><stop stopColor={dark} /><stop offset="1" stopColor={black} /></linearGradient>
        <radialGradient id={`glow-${type}`} cx="50%" cy="55%" r="60%"><stop stopColor="#ffe0a8" stopOpacity=".8" /><stop offset="1" stopColor={accent} stopOpacity="0" /></radialGradient>
        <pattern id={`stripe-${type}`} width="64" height="64" patternUnits="userSpaceOnUse" patternTransform="rotate(32)"><rect width="64" height="21" fill="#b3261e" /><rect y="21" width="64" height="22" fill="#efe6d4" /><rect y="43" width="64" height="21" fill="#1f4f7a" /></pattern>
      </defs>
      <rect width="800" height="600" fill={`url(#bg-${type})`} />
      <rect width="800" height="600" fill={`url(#glow-${type})`} opacity=".7" />
      {type === 'arco' && <><path d="M255 600V270a145 145 0 0 1 290 0V600Z" fill={`url(#glow-${type})`} /><path d="M400 125V600M255 330H545M255 450H545" stroke="#2a1409" strokeWidth="7" /><g fill="#080403"><rect x="345" y="395" width="110" height="160" rx="42" /><rect x="326" y="505" width="148" height="52" rx="22" /><rect x="312" y="462" width="32" height="82" rx="13" /><rect x="456" y="462" width="32" height="82" rx="13" /></g></>}
      {type === 'espejo' && <><path d="M300 520V210a100 100 0 0 1 200 0V520Z" fill={`url(#glow-${type})`} stroke="#b9803f" strokeWidth="12" /><path d="M330 480V230a70 70 0 0 1 70-70" fill="none" stroke="#fff" strokeOpacity=".28" strokeWidth="5" /><rect x="150" y="530" width="500" height="10" fill="#b9803f" /><g fill="#120c08" stroke="#b9803f" strokeWidth="2"><rect x="190" y="470" width="26" height="60" rx="8" /><rect x="598" y="462" width="28" height="68" rx="9" /></g></>}
      {type === 'poste' && <><rect x="362" y="70" width="76" height="470" rx="38" fill={`url(#stripe-${type})`} /><rect x="362" y="70" width="76" height="470" rx="38" fill="#fff" opacity=".12" /><rect x="352" y="48" width="96" height="34" rx="12" fill="#b9803f" /><rect x="352" y="528" width="96" height="34" rx="12" fill="#b9803f" /></>}
      {type === 'navaja' && <><g transform="rotate(-22 400 300)"><rect x="200" y="282" width="230" height="38" rx="14" fill="#b9803f" /><path d="M424 288L612 270Q640 296 612 326L424 316Z" fill="#d5d0c6" /><path d="M430 296L604 281" stroke="#fff" strokeWidth="3" opacity=".7" /><circle cx="430" cy="301" r="13" fill="#2a1a0c" stroke="#b9803f" strokeWidth="3" /></g><ellipse cx="350" cy="467" rx="44" ry="30" fill="#efe6d4" /></>}
      {type === 'fachada' && <><rect width="800" height="600" fill="#6c3a28" opacity=".55" /><path d="M150 250L650 250L690 170L110 170Z" fill="#7b1d18" /><path d="M340 600V360a60 60 0 0 1 120 0V600Z" fill={`url(#glow-${type})`} /><rect x="300" y="100" width="200" height="56" rx="6" fill="#0c0705" stroke="#b9803f" strokeWidth="3" /><text x="400" y="140" textAnchor="middle" fontFamily="Bodoni Moda, Georgia, serif" fontStyle="italic" fontSize="38" fill="#efe6d4">Arena</text><rect x="150" y="320" width="130" height="170" rx="8" fill={`url(#glow-${type})`} opacity=".85" /><rect x="520" y="320" width="130" height="170" rx="8" fill={`url(#glow-${type})`} opacity=".85" /></>}
      <rect width="800" height="600" fill="#fff" opacity=".035" />
    </svg>
  );
}

function Plate({ type, caption, className = '' }) {
  return <figure className={`plate ${className}`}><img src={photoSources[type]} alt="" loading="lazy" /><Art type={type} /><figcaption className="mono">{caption}</figcaption></figure>;
}

const treatments = [
  ['Corte clásico', '$350 MXN', '45 min · tijera y máquina, lavado y peinado'],
  ['Afeitado con toalla caliente', '$300 MXN', '40 min · navaja, vapor y aceite de cedro'],
  ['Barba perfilada', '$220 MXN', '30 min · contorno con navaja y bálsamo'],
  ['Ritual Arena', '$620 MXN', '90 min · corte, afeitado y mascarilla de arcilla'],
];

function SplitText({ children }) {
  return <>{[...children].map((char, index) => <span className="ch" style={{ '--c': index }} key={`${char}-${index}`}>{char}</span>)}</>;
}

function App() {
  const canvasRef = useRef(null);
  const formRef = useRef(null);
  const noteRef = useRef(null);

  useEffect(() => {
    const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const lenis = new Lenis({ autoRaf: false, lerp: reduce ? 1 : .085, smoothWheel: true, syncTouch: true });
    const lenisTicker = (time) => lenis.raf(time * 1000);
    gsap.ticker.add(lenisTicker);
    gsap.ticker.lagSmoothing(0);
    const handleAnchor = (event) => { const link = event.target.closest('a[href^="#"]'); if (!link) return; const target = document.querySelector(link.getAttribute('href')); if (!target) return; event.preventDefault(); history.replaceState(null, '', link.getAttribute('href')); const top = target.id === 'inicio' ? 0 : target.offsetTop; lenis.scrollTo(top, { duration: reduce ? 0 : 1.15, offset: 0 }); };
    const canvas = canvasRef.current;
    let gl = null;
    const uniforms = {};
    const cursor = document.querySelector('.cursor');
    const progress = document.querySelector('#bar');
    const where = document.querySelector('#where');
    const sections = [['inicio', 'Inicio'], ['manifiesto', 'Manifiesto'], ['casa', 'La casa'], ['tratamientos', 'Tratamientos'], ['pelicula', 'Película'], ['nosotros', 'Nosotros']];
    const manifest = document.querySelector('#manifiesto');
    const manifestText = document.querySelector('.mtext');
    const manifestImage = document.querySelector('.mimg');
    const film = document.querySelector('#pelicula');
    const frames = [...document.querySelectorAll('.frame')];
    const captions = [...document.querySelectorAll('.fcap')];
    const plates = [...document.querySelectorAll('.px')];
    const metrics = {};
    let animationFrame; let width = innerWidth; let height = innerHeight; let smoothScroll = scrollY; let shaderLevel = 0; let velocity = 0; let mouseX = .5; let mouseY = .5; let mouseStrength = 0; let targetCursorX = -100; let targetCursorY = -100; let cursorX = -100; let cursorY = -100; let startTime = performance.now();
    const clamp = (value, min = 0, max = 1) => Math.min(max, Math.max(min, value));
    const smooth = (value) => { const t = clamp(value); return t * t * (3 - 2 * t); };
    const resize = () => { width = innerWidth; height = innerHeight; if (gl && canvas) { const dpr = Math.min(devicePixelRatio || 1, 1.25); canvas.width = Math.round(width * dpr); canvas.height = Math.round(height * dpr); gl.viewport(0, 0, canvas.width, canvas.height); } measure(); };
    const move = (event) => { mouseX = event.clientX / width; mouseY = 1 - event.clientY / height; mouseStrength = 1; targetCursorX = event.clientX; targetCursorY = event.clientY; };
    const hover = (event) => cursor?.classList.toggle('big', Boolean(event.target.closest('a,button,input,[data-hover],.plate')));
    const measure = () => { sections.forEach(([id]) => { const section = document.getElementById(id); if (section) metrics[id] = { top: section.getBoundingClientRect().top + scrollY, height: section.offsetHeight }; }); plates.forEach((element) => { element.dataset.top = String(element.getBoundingClientRect().top + scrollY); }); };
    const levelAt = (position) => { const start = metrics.inicio.top + metrics.inicio.height * .82; const end = metrics.manifiesto.top + height * .08; const drainStart = metrics.nosotros.top - height * .95; const drainEnd = metrics.nosotros.top - height * .1; return smooth((position - start) / (end - start)) * (1 - smooth((position - drainStart) / (drainEnd - drainStart))); };
    const vertex = 'attribute vec2 a;void main(){gl_Position=vec4(a,0.,1.);}';
    const fragment = `
#ifdef GL_FRAGMENT_PRECISION_HIGH
precision highp float;
#else
precision mediump float;
#endif
uniform vec2 uRes;uniform float uTime;uniform float uLevel;uniform float uVel;uniform vec2 uMouse;uniform float uMouseS;
float hash(vec2 p){p=fract(p*vec2(123.34,456.21));p+=dot(p,p+45.32);return fract(p.x*p.y);}
float noise(vec2 p){vec2 i=floor(p),f=fract(p);f=f*f*(3.-2.*f);return mix(mix(hash(i),hash(i+vec2(1,0)),f.x),mix(hash(i+vec2(0,1)),hash(i+vec2(1,1)),f.x),f.y);}
float fbm(vec2 p){float v=0.,a=.5;mat2 m=mat2(1.6,1.2,-1.2,1.6);for(int i=0;i<5;i++){v+=a*noise(p);p=m*p;a*=.5;}return v;}
void main(){
  vec2 uv=gl_FragCoord.xy/uRes;float asp=uRes.x/uRes.y;vec2 p=vec2(uv.x*asp,uv.y);float t=uTime*.1;
  vec2 q=vec2(fbm(p*1.4+vec2(0.,t)),fbm(p*1.4+vec2(5.2,1.3)-t));
  float f=fbm(p*1.2+2.*q+vec2(t*.6,-t*.4));
  float md=distance(p,vec2(uMouse.x*asp,uMouse.y));
  float ripple=sin(md*26.-uTime*3.)*exp(-md*4.5)*.03*uMouseS;
  float h=mix(-.4,1.55,uLevel);float amp=.55+uVel*.4;float edgeNoise=fbm(vec2(p.x*3.1,p.y*10.0+q.x*2.));float field=h+(f-.5)*amp+(edgeNoise-.5)*.045+ripple+sin(p.x*2.2+uTime*.5)*.014;float d=field-uv.y;
  float aa=1.6/uRes.y;float ink=smoothstep(-aa,aa,d);vec3 bone=vec3(.925,.909,.875);vec3 blk=vec3(.043,.039,.035);float vig=smoothstep(1.25,.2,distance(uv,vec2(.5)));bone*=.955+.045*vig;bone-=(fbm(p*7.)-.5)*.018;
  float sh=exp(-max(-d,0.)*20.)*(1.-ink);bone*=1.-.26*sh;float depth=clamp(d*3.,0.,1.);vec3 inkc=blk+vec3(.055,.038,.022)*f*(1.-depth*.65);float rim=exp(-max(d,0.)*60.)*ink;float streak=pow(fbm(p*vec2(3.,11.)+q*3.),3.);float foam=smoothstep(.34,.82,edgeNoise)*exp(-abs(d)*75.);vec3 copper=vec3(.78,.52,.25);inkc+=copper*rim*(.22+.7*streak)+copper*foam*.18;bone=mix(bone,vec3(1.,.96,.85),foam*.12);
  vec3 col=mix(bone,inkc,ink);col+=(hash(gl_FragCoord.xy+fract(uTime)*97.)-.5)*.05;gl_FragColor=vec4(col,1.);
}`;
    try { gl = canvas?.getContext('webgl', { antialias: false, alpha: false, powerPreference: 'high-performance' }); if (gl) { const compile = (type, source) => { const shader = gl.createShader(type); gl.shaderSource(shader, source); gl.compileShader(shader); return shader; }; const program = gl.createProgram(); gl.attachShader(program, compile(gl.VERTEX_SHADER, vertex)); gl.attachShader(program, compile(gl.FRAGMENT_SHADER, fragment)); gl.linkProgram(program); gl.useProgram(program); const buffer = gl.createBuffer(); gl.bindBuffer(gl.ARRAY_BUFFER, buffer); gl.bufferData(gl.ARRAY_BUFFER, new Float32Array([-1, -1, 1, -1, -1, 1, 1, 1]), gl.STATIC_DRAW); const location = gl.getAttribLocation(program, 'a'); gl.enableVertexAttribArray(location); gl.vertexAttribPointer(location, 2, gl.FLOAT, false, 0, 0); ['uRes', 'uTime', 'uLevel', 'uVel', 'uMouse', 'uMouseS'].forEach((name) => { uniforms[name] = gl.getUniformLocation(program, name); }); } } catch { gl = null; }
    const draw = (time) => {
      const target = lenis.scroll; const previous = smoothScroll; smoothScroll += (target - smoothScroll) * (reduce ? 1 : .18); if (Math.abs(target - smoothScroll) < .05) smoothScroll = target; velocity += (clamp(Math.abs(smoothScroll - previous) / height * 9) - velocity) * .1; mouseStrength *= .965; cursorX += (targetCursorX - cursorX) * .2; cursorY += (targetCursorY - cursorY) * .2; if (cursor) cursor.style.transform = `translate3d(${cursorX}px,${cursorY}px,0)`;
      const targetLevel = levelAt(smoothScroll); shaderLevel += (targetLevel - shaderLevel) * (reduce ? 1 : .032); if (gl && canvas) { gl.uniform2f(uniforms.uRes, canvas.width, canvas.height); gl.uniform1f(uniforms.uTime, (time - startTime) / 1000); gl.uniform1f(uniforms.uLevel, shaderLevel); gl.uniform1f(uniforms.uVel, velocity); gl.uniform2f(uniforms.uMouse, mouseX, mouseY); gl.uniform1f(uniforms.uMouseS, mouseStrength); gl.drawArrays(gl.TRIANGLE_STRIP, 0, 4); }
      plates.forEach((element) => { const speed = parseFloat(element.dataset.speed) || 0; const mouseOffset = parseFloat(element.dataset.mx) || 0; const mx = mouseOffset * (mouseX - .5); const rotate = mouseOffset * (mouseX - .5) * .035; const translateY = element.classList.contains('float') ? -smoothScroll * speed : (smoothScroll - parseFloat(element.dataset.top || 0) + height * .5) * speed * -1; element.style.transform = `translate3d(${mx}px,${translateY}px,0) rotate(${rotate}deg)`; });
      if (manifest && manifestText && manifestImage) { const progressValue = clamp((smoothScroll - metrics.manifiesto.top) / (metrics.manifiesto.height - height)); const textWidth = manifestText.scrollWidth; manifestText.style.left = `${width * .55 - progressValue * (textWidth + width * .6)}px`; manifestText.style.transform = 'translateY(-50%)'; const opening = smooth((progressValue - .02) / .16); const inset = (1 - opening) * 50; manifestImage.style.clipPath = `inset(${inset}% ${inset}% ${inset}% ${inset}%)`; manifestImage.style.transform = `translateY(${(.5 - progressValue) * 150}px) scale(${1 + (1 - opening) * .5})`; }
      if (film) { const filmProgress = clamp((smoothScroll - metrics.pelicula.top) / (metrics.pelicula.height - height)); const position = filmProgress * 3; const opacities = frames.map((frame, index) => { let distance = Math.abs(position - (index + .5)); if (index === 0 && position < .5) distance = 0; if (index === 2 && position > 2.5) distance = 0; const opacity = clamp(1 - (distance - .34) / .3); frame.style.opacity = opacity; const plate = frame.firstElementChild; if (plate) plate.style.transform = `scale(${1.04 + clamp(position - index, 0, 1) * .14})`; return opacity; }); const active = opacities.indexOf(Math.max(...opacities)); captions.forEach((caption, index) => caption.classList.toggle('on', index === active && opacities[index] > .5)); const counter = document.querySelector('.fcount'); if (counter) counter.textContent = ['I / III', 'II / III', 'III / III'][active]; }
      let label = sections[0][1]; sections.forEach(([id, name]) => { if (smoothScroll + height * .5 >= metrics[id]?.top) label = name; }); if (where) where.textContent = label; if (progress) progress.style.transform = `scaleY(${clamp(smoothScroll / Math.max(1, document.documentElement.scrollHeight - height))})`; animationFrame = requestAnimationFrame(draw);
    };

    resize(); window.addEventListener('resize', resize); window.addEventListener('pointermove', move, { passive: true }); document.addEventListener('mouseover', hover); document.addEventListener('click', handleAnchor);
    if (window.location.hash) { const initialTarget = document.querySelector(window.location.hash); if (initialTarget) requestAnimationFrame(() => { const top = initialTarget.id === 'inicio' ? 0 : initialTarget.offsetTop; lenis.scrollTo(top, { immediate: true }); window.scrollTo(0, top); }); }
    document.documentElement.classList.add('js');
    const heroTargets = ['.hero-kicker', '.hero .eyebrow', '.hero .h1 .ln > span', '.hero .btn', '.hero-note'];
    gsap.set(heroTargets, { animation: 'none' });
    gsap.set(['.hero-kicker', '.hero .eyebrow', '.hero .btn', '.hero-note'], { autoAlpha: 0, y: 18 });
    gsap.set('.hero .h1 .ln > span', { y: '112%' });
    const heroEntrance = gsap.timeline({ defaults: { ease: 'power3.out' } });
    heroEntrance.to('.hero-kicker', { autoAlpha: 1, y: 0, duration: .8 }, 2.65).to('.hero .eyebrow', { autoAlpha: 1, y: 0, duration: .8 }, 2.8).to('.hero .h1 .ln > span', { y: 0, duration: 1.15, stagger: .11 }, 2.75).to('.hero .btn', { autoAlpha: 1, y: 0, duration: .8 }, 3.35).to('.hero-note', { autoAlpha: .65, y: 0, duration: .8 }, 3.45);
    const revealObserver = new IntersectionObserver((entries) => entries.forEach((entry) => { if (entry.isIntersecting) entry.target.classList.add('in'); }), { threshold: .12 });
    document.querySelectorAll('.rv').forEach((element) => revealObserver.observe(element));
    const titleObserver = new IntersectionObserver((entries) => entries.forEach((entry) => entry.target.classList.toggle('on', entry.isIntersecting)), { threshold: .3 });
    const title = document.querySelector('.big'); if (title) titleObserver.observe(title);
    const submit = (event) => { event.preventDefault(); const value = new FormData(formRef.current).get('mail')?.trim() || ''; noteRef.current.textContent = /^\S+@\S+\.\S+$/.test(value) ? 'Listo. Esto es una demo: el correo no se envía a ningún lado.' : 'Escribe un correo válido.'; };
    formRef.current?.addEventListener('submit', submit);
    animationFrame = requestAnimationFrame(draw);
    return () => { cancelAnimationFrame(animationFrame); gsap.ticker.remove(lenisTicker); heroEntrance.kill(); lenis.destroy(); window.removeEventListener('resize', resize); window.removeEventListener('pointermove', move); document.removeEventListener('mouseover', hover); document.removeEventListener('click', handleAnchor); revealObserver.disconnect(); titleObserver.disconnect(); formRef.current?.removeEventListener('submit', submit); };
  }, []);

  return <>
    <canvas id="gl" ref={canvasRef} aria-hidden="true" />
    <div className="cursor" aria-hidden="true" />
    <div id="intro" aria-hidden="true"><div className="neon">Arena</div><div className="tag mono">Barbería de navaja · Roma Norte</div></div>
    <header className="nav bl"><a className="logo" href="#inicio" aria-label="Arena, inicio">Arena</a><nav className="links mono" aria-label="Principal"><a href="#tratamientos">Tratamientos</a><a href="#casa">La casa</a><a href="#pelicula">Película</a><a href="#nosotros">Nosotros</a></nav><a className="res mono" href="#nosotros">Reservar</a></header>
    <div className="where mono" aria-hidden="true"><span id="where">Inicio</span></div><div className="bar" aria-hidden="true"><i id="bar" /></div>
    <main>
      <section className="hero" id="inicio"><div className="float f1 px" data-speed=".16" data-mx="26"><Plate type="arco" caption="Sillón · 18:40" /></div><div className="float f2 px" data-speed=".34" data-mx="-34"><Plate type="poste" caption="Poste" /></div><div className="float f3 px" data-speed=".5" data-mx="22"><Plate type="navaja" caption="Navaja" /></div><div className="float f4 px" data-speed=".24" data-mx="-18"><Plate type="espejo" caption="Espejo" /></div><div className="hero-inner wrap"><div className="hero-kicker"><span className="mono bl">Arena / 001</span><span className="hero-status mono bl"><i /> Abierto hoy · 10:00—21:00</span></div><p className="eyebrow mono bl">Barbería de navaja · Roma Norte, CDMX</p><h1 className="h1 bl" aria-label="Un ritual de arena y navaja"><span className="ln"><span style={{ '--i': 0 }}>Un ritual</span></span><span className="ln"><span style={{ '--i': 1 }}><em>de arena</em></span></span><span className="ln"><span style={{ '--i': 2 }}>y navaja</span></span></h1><div className="hero-actions"><a className="btn" href="#nosotros" data-hover>Reservar turno <span aria-hidden="true">→</span></a><span className="hero-note mono bl">Cuatro sillones<br />Una sola navaja</span></div></div><div className="hero-foot wrap"><span className="mono bl">Roma Norte · CDMX</span><span className="scroll-cue mono bl"><i /> Desplázate para entrar</span><span className="mono bl">19° 26′ 12″ N</span></div></section>
      <div className="bridge bl"><span className="mono">Baja despacio</span><i /></div>
      <section className="manifest" id="manifiesto" aria-label="Manifiesto"><div className="stage"><div className="mtext" aria-hidden="true">Identidad. <em>El ritual como expresión.</em> El cuidado personal como elegancia.&nbsp;&nbsp;</div><div className="mimg plate"><img src={photoSources.fachada} alt="Interior de barbería con ladrillo y madera" /><Art type="fachada" /><figcaption className="mono">Fachada · Calle Durango</figcaption></div><p className="mnote mono">Identidad. El ritual como expresión. El cuidado personal como elegancia.</p></div></section>
      <section className="collage wrap" id="casa"><div className="casa-heading"><h2 className="h2">La casa</h2><p className="lead">Roma Norte · desde 2019</p></div><div className="grid"><div className="px c1" data-speed="-.08" data-mx="-30"><div className="rv"><Plate type="arco" caption="Planta baja" /></div></div><div className="px c2" data-speed=".11" data-mx="34"><div className="rv"><Plate type="espejo" caption="Estación 2" /></div></div><div className="px c3" data-speed="-.06" data-mx="-26"><div className="rv"><Plate type="navaja" caption="Acero de Solingen" /></div></div><div className="px c4" data-speed=".14" data-mx="28"><div className="rv"><Plate type="poste" caption="Desde 2019" /></div></div></div></section>
      <section className="menu wrap" id="tratamientos"><h2 className="h2 wide">Tratamientos</h2><div className="rows">{treatments.map(([name, price, description]) => <div className="row" data-hover key={name}><span className="nm">{name === 'Ritual Arena' ? <em>{name}</em> : name}</span><span className="pr">{price}</span><span className="du mono">{description}</span></div>)}</div></section>
      <section className="film" id="pelicula" aria-label="La película"><div className="stage"><div className="frame"><div className="plate"><img src={photoSources.arco} alt="Sillón de barbería" /><Art type="arco" /></div></div><div className="frame"><div className="plate"><img src={photoSources.espejo} alt="Espejo de barbería" /><Art type="espejo" /></div></div><div className="frame"><div className="plate"><img src={photoSources.poste} alt="Poste de barbería" /><Art type="poste" /></div></div><p className="fcap on"><SplitText>Diseñada alrededor de la luz de las seis de la tarde.</SplitText></p><p className="fcap"><SplitText>Cada sillón mira al espejo, nunca a la puerta.</SplitText></p><p className="fcap"><SplitText>Una navaja, una toalla caliente y nada de prisa.</SplitText></p><div className="ftop mono">La película · Cómo se diseñó Arena</div><div className="fnext mono">Sigue bajando ↓</div><div className="fcount mono">I / III</div></div></section>
      <section className="about wrap" id="nosotros"><h2 className="big bl"><SplitText>Nosotros</SplitText></h2><div className="cols bl"><p>Arena nació de una idea simple: que cortarse el pelo sea el único rato del día sin prisa. Navaja, toalla caliente y tiempo.</p><div><p className="mono intro-copy">Recibe noticias de Arena: aperturas, colaboraciones y las fechas libres de cada barbero.</p><form className="sub" ref={formRef} noValidate><label htmlFor="mail" className="sr-only">Correo</label><input id="mail" name="mail" type="email" placeholder="tu@correo.com" autoComplete="email" /><button type="submit" data-hover>Suscribirme →</button></form><div className="note mono" ref={noteRef} role="status" /></div></div><footer className="foot mono bl"><nav aria-label="Pie"><a href="#tratamientos">Tratamientos</a><a href="#casa">La casa</a><a href="#pelicula">Película</a><a href="#inicio">Volver arriba ↑</a></nav><span>Roma Norte, CDMX · Lun–Sáb 10:00–21:00</span></footer></section>
    </main>
  </>;
}

createRoot(document.getElementById('root')).render(<App />);
