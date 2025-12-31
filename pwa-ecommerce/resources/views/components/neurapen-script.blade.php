<div id="__framer-badge-container"></div>

<script>
    var animator = (() => {
        var be = ["transformPerspective", "x", "y", "z", "translateX", "translateY", "translateZ", "scale", "scaleX", "scaleY", "rotate", "rotateX", "rotateY", "rotateZ", "skew", "skewX", "skewY"],
            q = new Set(be);
        var S = e => e * 1e3,
            k = e => e / 1e3;
        var E = e => e;
        var W = E;
        var z = (e, t, n) => n > t ? t : n < e ? e : n;

        function H(e, t) {
            return t ? e * (1e3 / t) : 0
        }
        var ve = 5;

        function U(e, t, n) {
            let o = Math.max(t - ve, 0);
            return H(n - e(o), t - o)
        }
        var B = .001,
            Oe = .01,
            Q = 10,
            Se = .05,
            ke = 1;

        function J({
            duration: e = 800,
            bounce: t = .25,
            velocity: n = 0,
            mass: o = 1
        }) {
            let f, r;
            W(e <= S(Q), "Spring duration must be 10 seconds or less");
            let a = 1 - t;
            a = z(Se, ke, a), e = z(Oe, Q, k(e)), a < 1 ? (f = s => {
                let p = s * a,
                    c = p * e,
                    u = p - n,
                    l = C(s, a),
                    d = Math.exp(-c);
                return B - u / l * d
            }, r = s => {
                let c = s * a * e,
                    u = c * n + n,
                    l = Math.pow(a, 2) * Math.pow(s, 2) * e,
                    d = Math.exp(-c),
                    g = C(Math.pow(s, 2), a);
                return (-f(s) + B > 0 ? -1 : 1) * ((u - l) * d) / g
            }) : (f = s => {
                let p = Math.exp(-s * e),
                    c = (s - n) * e + 1;
                return -B + p * c
            }, r = s => {
                let p = Math.exp(-s * e),
                    c = (n - s) * (e * e);
                return p * c
            });
            let m = 5 / e,
                i = De(f, r, m);
            if (e = S(e), isNaN(i)) return {
                stiffness: 100,
                damping: 10,
                duration: e
            };
            {
                let s = Math.pow(i, 2) * o;
                return {
                    stiffness: s,
                    damping: a * 2 * Math.sqrt(o * s),
                    duration: e
                }
            }
        }
        var Pe = 12;

        function De(e, t, n) {
            let o = n;
            for (let f = 1; f < Pe; f++) o = o - e(o) / t(o);
            return o
        }

        function C(e, t) {
            return e * Math.sqrt(1 - t * t)
        }
        var Ie = ["duration", "bounce"],
            Ke = ["stiffness", "damping", "mass"];

        function ee(e, t) {
            return t.some(n => e[n] !== void 0)
        }

        function Ee(e) {
            let t = {
                velocity: 0,
                stiffness: 100,
                damping: 10,
                mass: 1,
                isResolvedFromDuration: !1,
                ...e
            };
            if (!ee(e, Ke) && ee(e, Ie)) {
                let n = J(e);
                t = {
                    ...t,
                    ...n,
                    mass: 1
                }, t.isResolvedFromDuration = !0
            }
            return t
        }

        function P({
            keyframes: e,
            restDelta: t,
            restSpeed: n,
            ...o
        }) {
            let f = e[0],
                r = e[e.length - 1],
                a = {
                    done: !1,
                    value: f
                },
                {
                    stiffness: m,
                    damping: i,
                    mass: s,
                    duration: p,
                    velocity: c,
                    isResolvedFromDuration: u
                } = Ee({
                    ...o,
                    velocity: -k(o.velocity || 0)
                }),
                l = c || 0,
                d = i / (2 * Math.sqrt(m * s)),
                g = r - f,
                y = k(Math.sqrt(m / s)),
                M = Math.abs(g) < 5;
            n || (n = M ? .01 : 2), t || (t = M ? .005 : .5);
            let h;
            if (d < 1) {
                let x = C(y, d);
                h = A => {
                    let T = Math.exp(-d * y * A);
                    return r - T * ((l + d * y * g) / x * Math.sin(x * A) + g * Math.cos(x * A))
                }
            } else if (d === 1) h = x => r - Math.exp(-y * x) * (g + (l + y * g) * x);
            else {
                let x = y * Math.sqrt(d * d - 1);
                h = A => {
                    let T = Math.exp(-d * y * A),
                        K = Math.min(x * A, 300);
                    return r - T * ((l + d * y * g) * Math.sinh(K) + x * g * Math.cosh(K)) / x
                }
            }
            return {
                calculatedDuration: u && p || null,
                next: x => {
                    let A = h(x);
                    if (u) a.done = x >= p;
                    else {
                        let T = 0;
                        d < 1 && (T = x === 0 ? S(l) : U(h, x, A));
                        let K = Math.abs(T) <= n,
                            Te = Math.abs(r - A) <= t;
                        a.done = K && Te
                    }
                    return a.value = a.done ? r : A, a
                }
            }
        }
        var te = e => Array.isArray(e) && typeof e[0] == "number";
        var ne = (e, t, n) => {
            let o = t - e;
            return o === 0 ? 1 : (n - e) / o
        };
        var Ce = 10,
            oe = (e, t) => {
                let n = "",
                    o = Math.max(Math.round(t / Ce), 2);
                for (let f = 0; f < o; f++) n += e(ne(0, o - 1, f)) + ", ";
                return `linear(${n.substring(0,n.length-2)})`
            };

        function re(e) {
            let t;
            return () => (t === void 0 && (t = e()), t)
        }
        var ie = {
            linearEasing: void 0
        };

        function se(e, t) {
            let n = re(e);
            return () => {
                var o;
                return (o = ie[t]) !== null && o !== void 0 ? o : n()
            }
        }
        var ae = se(() => {
            try {
                document.createElement("div").animate({
                    opacity: 0
                }, {
                    easing: "linear(0, 1)"
                })
            } catch {
                return !1
            }
            return !0
        }, "linearEasing");
        var D = ([e, t, n, o]) => `cubic-bezier(${e}, ${t}, ${n}, ${o})`,
            pe = {
                linear: "linear",
                ease: "ease",
                easeIn: "ease-in",
                easeOut: "ease-out",
                easeInOut: "ease-in-out",
                circIn: D([0, .65, .55, 1]),
                circOut: D([.55, 0, 1, .45]),
                backIn: D([.31, .01, .66, -.59]),
                backOut: D([.33, 1.53, .69, .99])
            };

        function L(e, t) {
            if (e) return typeof e == "function" && ae() ? oe(e, t) : te(e) ? D(e) : Array.isArray(e) ? e.map(n => L(n, t) || pe.easeOut) : pe[e]
        }

        function R(e, t, n, {
            delay: o = 0,
            duration: f = 300,
            repeat: r = 0,
            repeatType: a = "loop",
            ease: m,
            times: i
        } = {}) {
            let s = {
                [t]: n
            };
            i && (s.offset = i);
            let p = L(m, f);
            return Array.isArray(p) && (s.easing = p), e.animate(s, {
                delay: o,
                duration: f,
                easing: Array.isArray(p) ? "linear" : p,
                fill: "both",
                iterations: r + 1,
                direction: a === "reverse" ? "alternate" : "normal"
            })
        }
        var fe = e => e.replace(/([a-z])([A-Z])/gu, "$1-$2").toLowerCase();
        var F = "framerAppearId",
            me = "data-" + fe(F);

        function ce(e) {
            return e.props[me]
        }
        var b = (e, t) => {
            let n = q.has(t) ? "transform" : t;
            return `${e}: ${n}`
        };
        var w = new Map,
            v = new Map;

        function X(e, t, n) {
            var o;
            let f = b(e, t),
                r = w.get(f);
            if (!r) return null;
            let {
                animation: a,
                startTime: m
            } = r;

            function i() {
                var s;
                (s = window.MotionCancelOptimisedAnimation) === null || s === void 0 || s.call(window, e, t, n)
            }
            return a.onfinish = i, m === null || !((o = window.MotionHandoffIsComplete) === null || o === void 0) && o.call(window, e) ? (i(), null) : m
        }
        var V, O, Y = new Set;

        function Ve() {
            Y.forEach(e => {
                e.animation.play(), e.animation.startTime = e.startTime
            }), Y.clear()
        }

        function j(e, t, n, o, f) {
            if (window.MotionIsMounted) return;
            let r = e.dataset[F];
            if (!r) return;
            window.MotionHandoffAnimation = X;
            let a = b(r, t);
            O || (O = R(e, t, [n[0], n[0]], {
                duration: 1e4,
                ease: "linear"
            }), w.set(a, {
                animation: O,
                startTime: null
            }), window.MotionHandoffAnimation = X, window.MotionHasOptimisedAnimation = (i, s) => {
                if (!i) return !1;
                if (!s) return v.has(i);
                let p = b(i, s);
                return !!w.get(p)
            }, window.MotionHandoffMarkAsComplete = i => {
                v.has(i) && v.set(i, !0)
            }, window.MotionHandoffIsComplete = i => v.get(i) === !0, window.MotionCancelOptimisedAnimation = (i, s, p, c) => {
                let u = b(i, s),
                    l = w.get(u);
                l && (p && c === void 0 ? p.postRender(() => {
                    p.postRender(() => {
                        l.animation.cancel()
                    })
                }) : l.animation.cancel(), p && c ? (Y.add(l), p.render(Ve)) : (w.delete(u), w.size || (window.MotionCancelOptimisedAnimation = void 0)))
            }, window.MotionCheckAppearSync = (i, s, p) => {
                var c, u;
                let l = ce(i);
                if (!l) return;
                let d = (c = window.MotionHasOptimisedAnimation) === null || c === void 0 ? void 0 : c.call(window, l, s),
                    g = (u = i.props.values) === null || u === void 0 ? void 0 : u[s];
                if (!d || !g) return;
                let y = p.on("change", M => {
                    var h;
                    g.get() !== M && ((h = window.MotionCancelOptimisedAnimation) === null || h === void 0 || h.call(window, l, s), y())
                });
                return y
            });
            let m = () => {
                O.cancel();
                let i = R(e, t, n, o);
                V === void 0 && (V = performance.now()), i.startTime = V, w.set(a, {
                    animation: i,
                    startTime: V
                }), f && f(i)
            };
            v.set(r, !1), O.ready ? O.ready.then(m).catch(E) : m()
        }
        var N = ["transformPerspective", "x", "y", "z", "translateX", "translateY", "translateZ", "scale", "scaleX", "scaleY", "rotate", "rotateX", "rotateY", "rotateZ", "skew", "skewX", "skewY"],
            $e = {
                x: "translateX",
                y: "translateY",
                z: "translateZ",
                transformPerspective: "perspective"
            },
            ze = {
                translateX: "px",
                translateY: "px",
                translateZ: "px",
                x: "px",
                y: "px",
                z: "px",
                perspective: "px",
                transformPerspective: "px",
                rotate: "deg",
                rotateX: "deg",
                rotateY: "deg"
            };

        function ue(e, t) {
            let n = ze[e];
            return !n || typeof t == "string" && t.endsWith(n) ? t : `${t}${n}`
        }

        function G(e) {
            return N.includes(e)
        }
        var Be = (e, t) => N.indexOf(e) - N.indexOf(t);

        function le({
            transform: e,
            transformKeys: t
        }, n) {
            let o = {},
                f = !0,
                r = "";
            t.sort(Be);
            for (let a of t) {
                let m = e[a],
                    i = !0;
                typeof m == "number" ? i = m === (a.startsWith("scale") ? 1 : 0) : i = parseFloat(m) === 0, i || (f = !1, r += `${$e[a]||a}(${e[a]}) `), n && (o[a] = e[a])
            }
            return r = r.trim(), n ? r = n(o, r) : f && (r = "none"), r
        }

        function _(e, t) {
            let n = new Set(Object.keys(e));
            for (let o in t) n.add(o);
            return Array.from(n)
        }

        function Z(e, t) {
            let n = t - e.length;
            if (n <= 0) return e;
            let o = new Array(n).fill(e[e.length - 1]);
            return e.concat(o)
        }

        function I(e) {
            return e * 1e3
        }
        var ge = {
                duration: .001
            },
            $ = {
                opacity: 1,
                scale: 1,
                translateX: 0,
                translateY: 0,
                translateZ: 0,
                x: 0,
                y: 0,
                z: 0,
                rotate: 0,
                rotateX: 0,
                rotateY: 0
            };

        function ye(e, t, n, o, f) {
            return n.delay && (n.delay = I(n.delay)), n.type === "spring" ? Re(e, t, n, o, f) : Xe(e, t, n, o, f)
        }

        function Le(e, t, n) {
            let o = {},
                f = 0,
                r = 0;
            for (let a of _(e, t)) {
                let m = e[a] ?? $[a],
                    i = t[a] ?? $[a];
                if (m === void 0 || i === void 0 || a !== "transformPerspective" && m === i) continue;
                a === "transformPerspective" && (o[a] = [m, i]);
                let s = _e(m, i, n),
                    {
                        duration: p,
                        keyframes: c
                    } = s;
                p === void 0 || c === void 0 || (p > f && (f = p, r = c.length), o[a] = c)
            }
            return {
                keyframeValuesByProps: o,
                longestDuration: f,
                longestLength: r
            }
        }

        function Re(e, t, n, o, f) {
            let r = {},
                {
                    keyframeValuesByProps: a,
                    longestDuration: m,
                    longestLength: i
                } = Le(e, t, n);
            if (!i) return r;
            let s = {
                    ease: "linear",
                    duration: m,
                    delay: n.delay
                },
                p = f ? ge : s,
                c = {};
            for (let [l, d] of Object.entries(a)) G(l) ? c[l] = Z(d, i) : r[l] = {
                keyframes: Z(d, i),
                options: l === "opacity" ? s : p
            };
            let u = he(c, o);
            return u && (r.transform = {
                keyframes: u,
                options: p
            }), r
        }

        function Fe(e) {
            let {
                type: t,
                duration: n,
                ...o
            } = e;
            return {
                duration: I(n),
                ...o
            }
        }

        function Xe(e, t, n, o, f) {
            let r = Fe(n);
            if (!r) return;
            let a = {},
                m = f ? ge : r,
                i = {};
            for (let p of _(e, t)) {
                let c = e[p] ?? $[p],
                    u = t[p] ?? $[p];
                c === void 0 || u === void 0 || p !== "transformPerspective" && c === u || (G(p) ? i[p] = [c, u] : a[p] = {
                    keyframes: [c, u],
                    options: p === "opacity" ? r : m
                })
            }
            let s = he(i, o);
            return s && (a.transform = {
                keyframes: s,
                options: m
            }), a
        }
        var Ye = ["duration", "bounce"],
            je = ["stiffness", "damping", "mass"];

        function Ae(e) {
            return je.some(t => t in e) ? !1 : Ye.some(t => t in e)
        }

        function Ne(e, t, n) {
            return Ae(n) ? `${e}-${t}-${n.duration}-${n.bounce}` : `${e}-${t}-${n.damping}-${n.stiffness}-${n.mass}`
        }

        function Ge(e) {
            return Ae(e) ? {
                ...e,
                duration: I(e.duration)
            } : e
        }
        var de = new Map,
            xe = 10;

        function _e(e, t, n) {
            let o = Ne(e, t, n),
                f = de.get(o);
            if (f) return f;
            let r = [e, t],
                a = P({
                    ...Ge(n),
                    keyframes: r
                }),
                m = {
                    done: !1,
                    value: r[0]
                },
                i = [],
                s = 0;
            for (; !m.done && s < I(10);) m = a.next(s), i.push(m.value), s += xe;
            r = i;
            let p = s - xe,
                u = {
                    keyframes: r,
                    duration: p,
                    ease: "linear"
                };
            return de.set(o, u), u
        }

        function he(e, t) {
            let n = [],
                o = Object.values(e)[0]?.length;
            if (!o) return;
            let f = Object.keys(e);
            for (let r = 0; r < o; r++) {
                let a = {};
                for (let [i, s] of Object.entries(e)) {
                    let p = s[r];
                    p !== void 0 && (a[i] = ue(i, p))
                }
                let m = le({
                    transform: a,
                    transformKeys: f
                }, t);
                n.push(m)
            }
            return n
        }

        function Ze(e, t) {
            if (!t)
                for (let n in e) {
                    let o = e[n];
                    return o?.legacy === !0 ? o : void 0
                }
        }

        function we(e, t, n, o, f, r) {
            for (let [a, m] of Object.entries(e)) {
                let i = r ? m[r] : void 0;
                if (i === null || !i && m.default === null) continue;
                let s = i ?? m.default ?? Ze(m, r);
                if (!s) continue;
                let {
                    initial: p,
                    animate: c,
                    transformTemplate: u
                } = s;
                if (!p || !c) continue;
                let {
                    transition: l,
                    ...d
                } = c, g = ye(p, d, l, qe(u, o), f);
                if (!g) continue;
                let y = {},
                    M = {};
                for (let [x, A] of Object.entries(g)) y[x] = A.keyframes, M[x] = A.options;
                let h = r ? `:not(.hidden-${r}) ` : "";
                t(`${h}[${n}="${a}"]`, y, M)
            }
        }

        function qe(e, t) {
            if (!(!e || !t)) return (n, o) => e.replace(t, o)
        }

        function Me(e) {
            return e ? e.find(n => n.mediaQuery ? window.matchMedia(n.mediaQuery).matches === !0 : !1)?.hash : void 0
        }
        var mn = {
            animateAppearEffects: we,
            getActiveVariantHash: Me,
            spring: P,
            startOptimizedAppearAnimation: j
        };
        return mn
    })()
</script>
<script type="framer/appear" id="__framer__appearAnimationsContent">{"3g24kz":{"default":{"initial":{"opacity":0.001,"rotate":0,"rotateX":0,"rotateY":0,"scale":1,"skewX":0,"skewY":0,"x":0,"y":0},"animate":{"opacity":1,"rotate":0,"rotateX":0,"rotateY":0,"scale":1,"skewX":0,"skewY":0,"transition":{"bounce":0.2,"delay":0,"duration":0.4,"type":"spring"},"x":0,"y":0}},"1ez7xnl":{"initial":{"opacity":0.001,"rotate":0,"rotateX":0,"rotateY":0,"scale":1,"skewX":0,"skewY":0,"x":0,"y":0},"animate":{"opacity":1,"rotate":0,"rotateX":0,"rotateY":0,"scale":1,"skewX":0,"skewY":0,"transition":{"bounce":0.2,"delay":0,"duration":0.4,"type":"spring"},"x":0,"y":0}},"1fu346c":null}}</script>
<script type="framer/appear" id="__framer__breakpoints">[{"hash":"72rtr7","mediaQuery":"(min-width: 1200px)"},{"hash":"1fu346c","mediaQuery":"(min-width: 810px) and (max-width: 1199px)"},{"hash":"1ez7xnl","mediaQuery":"(max-width: 809px)"},{"hash":"wfn55x","mediaQuery":"(min-width: 1200px)"},{"hash":"1bj59a6","mediaQuery":"(min-width: 810px) and (max-width: 1199px)"},{"hash":"jyq5g3","mediaQuery":"(max-width: 809px)"}]</script>
<script data-framer-appear-animation="no-preference">
    (() => {
        function c(i, o, m) {
            if (window.__framer_disable_appear_effects_optimization__ || typeof animator > "u") return;
            let e = {
                detail: {
                    bg: document.hidden
                }
            };
            requestAnimationFrame(() => {
                let a = "framer-appear-start";
                performance.mark(a, e), animator.animateAppearEffects(JSON.parse(window.__framer__appearAnimationsContent.text), (s, p, d) => {
                    let t = document.querySelector(s);
                    if (t)
                        for (let [r, f] of Object.entries(p)) animator.startOptimizedAppearAnimation(t, r, f, d[r])
                }, i, o, m && window.matchMedia("(prefers-reduced-motion:reduce)").matches === !0, animator.getActiveVariantHash(JSON.parse(window.__framer__breakpoints.text)));
                let n = "framer-appear-end";
                performance.mark(n, e), performance.measure("framer-appear", {
                    start: a,
                    end: n,
                    detail: e.detail
                })
            })
        }
        return c
    })()("data-framer-appear-id", "__Appear_Animation_Transform__", false)
</script>
<link rel="modulepreload" fetchpriority="low" href="https://framerusercontent.com/sites/3kPqYfGaC8bJjdfsCY48RH/chunk-23Y4TIYA.mjs">
<link rel="modulepreload" fetchpriority="low" href="https://framerusercontent.com/sites/3kPqYfGaC8bJjdfsCY48RH/chunk-XQV25LGY.mjs">
<link rel="modulepreload" fetchpriority="low" href="https://framerusercontent.com/sites/3kPqYfGaC8bJjdfsCY48RH/chunk-BSKATT4Q.mjs">
<link rel="modulepreload" fetchpriority="low" href="https://framerusercontent.com/sites/3kPqYfGaC8bJjdfsCY48RH/chunk-A3IIQ6X3.mjs">
<link rel="modulepreload" fetchpriority="low" href="https://framerusercontent.com/sites/3kPqYfGaC8bJjdfsCY48RH/chunk-HZL4YIMB.mjs">
<link rel="modulepreload" fetchpriority="low" href="https://framerusercontent.com/sites/3kPqYfGaC8bJjdfsCY48RH/chunk-FL5CUVHG.mjs">
<link rel="modulepreload" fetchpriority="low" href="https://framerusercontent.com/sites/3kPqYfGaC8bJjdfsCY48RH/chunk-DAALVYJK.mjs">
<link rel="modulepreload" fetchpriority="low" href="https://framerusercontent.com/sites/3kPqYfGaC8bJjdfsCY48RH/chunk-U33GJOKJ.mjs">
<link rel="modulepreload" fetchpriority="low" href="https://framerusercontent.com/sites/3kPqYfGaC8bJjdfsCY48RH/chunk-QQWH7KJP.mjs">
<link rel="modulepreload" fetchpriority="low" href="https://framerusercontent.com/sites/3kPqYfGaC8bJjdfsCY48RH/chunk-UC6ZKEIS.mjs">
<link rel="modulepreload" fetchpriority="low" href="https://framerusercontent.com/sites/3kPqYfGaC8bJjdfsCY48RH/chunk-42U43NKG.mjs">
<link rel="modulepreload" fetchpriority="low" href="https://framerusercontent.com/sites/3kPqYfGaC8bJjdfsCY48RH/_fPwoKBrHccY6YNLGYEDnoXN13KQJ93krMtcAMkyIEE.IOQM4BMF.mjs">
<link rel="modulepreload" fetchpriority="low" href="https://framerusercontent.com/sites/3kPqYfGaC8bJjdfsCY48RH/chunk-Z6A5KJUT.mjs">
<link rel="modulepreload" fetchpriority="low" href="https://framerusercontent.com/sites/3kPqYfGaC8bJjdfsCY48RH/chunk-JFQO52DV.mjs">
<link rel="modulepreload" fetchpriority="low" href="https://framerusercontent.com/sites/3kPqYfGaC8bJjdfsCY48RH/chunk-CWZ5A4L6.mjs">
<link rel="modulepreload" fetchpriority="low" href="https://framerusercontent.com/sites/3kPqYfGaC8bJjdfsCY48RH/chunk-7OUKLHPA.mjs">
<link rel="modulepreload" fetchpriority="low" href="https://framerusercontent.com/sites/3kPqYfGaC8bJjdfsCY48RH/chunk-N4P54Z6A.mjs">
<link rel="modulepreload" fetchpriority="low" href="https://framerusercontent.com/sites/3kPqYfGaC8bJjdfsCY48RH/chunk-I72YK6MI.mjs">
<link rel="modulepreload" fetchpriority="low" href="https://framerusercontent.com/sites/3kPqYfGaC8bJjdfsCY48RH/chunk-ZFGEWWYJ.mjs">
<link rel="modulepreload" fetchpriority="low" href="https://framerusercontent.com/sites/3kPqYfGaC8bJjdfsCY48RH/script_main.MWUL3U4K.mjs">
<script type="module" async data-framer-bundle="main" fetchpriority="low" src="https://framerusercontent.com/sites/3kPqYfGaC8bJjdfsCY48RH/script_main.MWUL3U4K.mjs"></script>
<div id="svg-templates" style="position: absolute; overflow: hidden; bottom: 0; left: 0; width: 0; height: 0; z-index: 0; contain: strict" aria-hidden="true">
    <svg width="18" height="18" viewBox="0 0 18 18" fill="none" id="svg1494861936_4540">
        <g clip-path="url(#svg1494861936_4540_clip0_54_302)">
            <path d="M4.39018 0.253698L4.70338 1.2176C4.80051 1.51078 4.96488 1.77717 5.18335 1.99549C5.40182 2.2138 5.66833 2.37798 5.96158 2.4749L6.92638 2.7881L6.94618 2.7935C7.02052 2.81967 7.08491 2.86827 7.13046 2.93259C7.17601 2.99691 7.20047 3.07378 7.20047 3.1526C7.20047 3.23141 7.17601 3.30829 7.13046 3.37261C7.08491 3.43693 7.02052 3.48553 6.94618 3.5117L5.98138 3.8249C5.68796 3.9217 5.42127 4.08582 5.20264 4.30414C4.98401 4.52246 4.8195 4.78892 4.72228 5.0822L4.40998 6.0452C4.38381 6.11954 4.33521 6.18393 4.27089 6.22948C4.20657 6.27503 4.12969 6.29949 4.05088 6.29949C3.97206 6.29949 3.89519 6.27503 3.83087 6.22948C3.76655 6.18393 3.71795 6.11954 3.69178 6.0452L3.37858 5.0822C3.28208 4.78792 3.1179 4.52038 2.89922 4.30108C2.68055 4.08178 2.41348 3.91684 2.11948 3.8195L1.15468 3.5063C1.08034 3.48013 1.01595 3.43153 0.9704 3.36721C0.924852 3.30289 0.900391 3.22601 0.900391 3.1472C0.900391 3.06838 0.924852 2.99151 0.9704 2.92719C1.01595 2.86287 1.08034 2.81427 1.15468 2.7881L2.11948 2.4749C2.40923 2.37548 2.67197 2.21018 2.88701 1.99201C3.10206 1.77385 3.26355 1.50876 3.35878 1.2176L3.67198 0.254598C3.69796 0.179942 3.74654 0.115225 3.81097 0.0694271C3.87539 0.0236296 3.95248 -0.000976563 4.03153 -0.000976562C4.11058 -0.000976562 4.18766 0.0236296 4.25209 0.0694271C4.31652 0.115225 4.3651 0.179942 4.39108 0.254598M8.80558 7.3925L8.11618 7.1693C7.90699 7.09936 7.71694 6.98172 7.56105 6.82567C7.40516 6.66963 7.28771 6.47946 7.21798 6.2702L6.99298 5.5826C6.97435 5.52944 6.93965 5.48338 6.8937 5.45079C6.84775 5.41821 6.79281 5.4007 6.73648 5.4007C6.68015 5.4007 6.62521 5.41821 6.57926 5.45079C6.5333 5.48338 6.49861 5.52944 6.47998 5.5826L6.25678 6.2702C6.18832 6.47812 6.07279 6.66742 5.91918 6.82337C5.76558 6.97932 5.57804 7.09771 5.37118 7.1693L4.68268 7.3925C4.62952 7.41113 4.58346 7.44582 4.55087 7.49177C4.51829 7.53772 4.50079 7.59267 4.50079 7.649C4.50079 7.70533 4.51829 7.76027 4.55087 7.80622C4.58346 7.85217 4.62952 7.88686 4.68268 7.9055L5.37118 8.1296C5.58099 8.19957 5.77159 8.31751 5.92783 8.47406C6.08406 8.63061 6.20163 8.82145 6.27118 9.0314L6.49438 9.719C6.51301 9.77216 6.5477 9.81822 6.59366 9.8508C6.63961 9.88339 6.69455 9.90089 6.75088 9.90089C6.80721 9.90089 6.86215 9.88339 6.9081 9.8508C6.95405 9.81822 6.98875 9.77216 7.00738 9.719L7.23148 9.0314C7.3013 8.82204 7.41889 8.63181 7.57494 8.47576C7.73099 8.3197 7.92122 8.20212 8.13058 8.1323L8.81908 7.9091C8.87224 7.89046 8.9183 7.85577 8.95088 7.80982C8.98347 7.76387 9.00097 7.70893 9.00097 7.6526C9.00097 7.59627 8.98347 7.54132 8.95088 7.49537C8.9183 7.44942 8.87224 7.41473 8.81908 7.3961L8.80558 7.3925ZM6.75178 10.7999C6.51231 10.7996 6.27844 10.7275 6.08038 10.5929C5.88238 10.4489 5.73838 10.259 5.64838 10.034L5.41438 9.3122C5.37167 9.19486 5.28961 9.09594 5.18218 9.0323L3.19048 11.0222C2.89583 11.3174 2.69135 11.6905 2.60098 12.0977L1.81078 15.6527C1.79425 15.7267 1.79667 15.8036 1.81782 15.8764C1.83897 15.9492 1.87816 16.0154 1.93175 16.069C1.98534 16.1226 2.05161 16.1618 2.12439 16.183C2.19717 16.2041 2.27411 16.2065 2.34808 16.19L5.88148 15.4043C6.30249 15.3115 6.68798 15.0997 6.99208 14.7941L14.175 7.6121L14.4774 7.9136C14.6461 8.08237 14.7409 8.31125 14.7409 8.5499C14.7409 8.78855 14.6461 9.01742 14.4774 9.1862L13.6314 10.0322C13.547 10.1167 13.4996 10.2313 13.4997 10.3507C13.4998 10.4701 13.5473 10.5846 13.6318 10.6689C13.7163 10.7533 13.8309 10.8007 13.9503 10.8006C14.0697 10.8005 14.1842 10.753 14.2686 10.6685L15.1137 9.8225C15.4511 9.48495 15.6407 9.02719 15.6407 8.5499C15.6407 8.0726 15.4511 7.61485 15.1137 7.2773L14.8113 6.9749L15.4143 6.3719C15.9068 5.86782 16.1806 5.18991 16.1765 4.48521C16.1724 3.7805 15.8907 3.10583 15.3924 2.60752C14.894 2.10921 14.2194 1.82745 13.5147 1.82334C12.81 1.81924 12.1321 2.09313 11.628 2.5856L8.10898 6.1046C8.12938 6.1406 8.15578 6.1733 8.18818 6.2027C8.25118 6.2567 8.31418 6.3017 8.39518 6.3287L9.08008 6.5537C9.34108 6.6437 9.53908 6.7967 9.68308 6.9956C9.81808 7.1846 9.89998 7.4186 9.89998 7.6715C9.89998 7.9235 9.82798 8.1491 9.68398 8.3483C9.53998 8.5463 9.34198 8.6903 9.11698 8.7713L8.40418 9.0053C8.33218 9.0323 8.26018 9.0683 8.20618 9.1322C8.15218 9.1961 8.10718 9.2582 8.08018 9.3392L7.85518 10.025C7.76518 10.277 7.62118 10.466 7.42318 10.61C7.22518 10.754 6.99298 10.7999 6.75178 10.7999Z" fill="#F5F4F2" />
        </g>
        <defs>
            <clipPath id="svg1494861936_4540_clip0_54_302">
                <rect width="18" height="18" fill="white" />
            </clipPath>
        </defs>
    </svg>
    <svg width="69" height="74" viewBox="0 0 69 74" fill="none" id="svg1650024675_9765">
        <path d="M15.6161 1.88962L17.0177 6.20289C17.4523 7.51482 18.1878 8.70688 19.1654 9.68379C20.143 10.6607 21.3356 11.3954 22.6479 11.8291L26.9652 13.2306L27.0538 13.2547C27.3864 13.3719 27.6746 13.5893 27.8784 13.8771C28.0822 14.165 28.1917 14.509 28.1917 14.8616C28.1917 15.2143 28.0822 15.5583 27.8784 15.8461C27.6746 16.134 27.3864 16.3514 27.0538 16.4686L22.7365 17.8701C21.4235 18.3032 20.2301 19.0376 19.2517 20.0146C18.2734 20.9915 17.5373 22.1839 17.1022 23.4962L15.7047 27.8055C15.5876 28.1382 15.3702 28.4263 15.0823 28.6301C14.7945 28.8339 14.4505 28.9434 14.0978 28.9434C13.7452 28.9434 13.4012 28.8339 13.1133 28.6301C12.8255 28.4263 12.608 28.1382 12.4909 27.8055L11.0894 23.4962C10.6576 22.1794 9.92292 20.9822 8.94439 20.0009C7.96587 19.0196 6.77079 18.2815 5.45519 17.8459L1.1379 16.4444C0.805223 16.3273 0.517097 16.1098 0.313279 15.822C0.109461 15.5342 0 15.1902 0 14.8375C0 14.4848 0.109461 14.1408 0.313279 13.853C0.517097 13.5652 0.805223 13.3477 1.1379 13.2306L5.45519 11.8291C6.75179 11.3842 7.92747 10.6445 8.88976 9.66826C9.85205 8.69199 10.5747 7.50577 11.0008 6.20289L12.4023 1.89365C12.5186 1.55958 12.736 1.26998 13.0243 1.06504C13.3126 0.860108 13.6575 0.75 14.0113 0.75C14.365 0.75 14.7099 0.860108 14.9982 1.06504C15.2865 1.26998 15.5039 1.55958 15.6202 1.89365M35.3742 33.8344L32.2893 32.8356C31.3532 32.5227 30.5028 31.9962 29.8052 31.298C29.1076 30.5997 28.582 29.7487 28.27 28.8123L27.2632 25.7354C27.1798 25.4975 27.0246 25.2914 26.8189 25.1456C26.6133 24.9998 26.3675 24.9215 26.1154 24.9215C25.8633 24.9215 25.6175 24.9998 25.4118 25.1456C25.2062 25.2914 25.051 25.4975 24.9676 25.7354L23.9688 28.8123C23.6625 29.7427 23.1455 30.5898 22.4582 31.2877C21.7708 31.9855 20.9316 32.5153 20.0059 32.8356L16.925 33.8344C16.6871 33.9178 16.481 34.073 16.3352 34.2786C16.1894 34.4843 16.1111 34.7301 16.1111 34.9822C16.1111 35.2343 16.1894 35.4801 16.3352 35.6857C16.481 35.8913 16.6871 36.0466 16.925 36.13L20.0059 37.1328C20.9448 37.4459 21.7977 37.9737 22.4968 38.6742C23.196 39.3747 23.722 40.2287 24.0333 41.1682L25.032 44.245C25.1154 44.4829 25.2707 44.689 25.4763 44.8348C25.6819 44.9807 25.9277 45.059 26.1798 45.059C26.4319 45.059 26.6778 44.9807 26.8834 44.8348C27.089 44.689 27.2442 44.4829 27.3276 44.245L28.3304 41.1682C28.6428 40.2313 29.169 39.3801 29.8673 38.6818C30.5656 37.9835 31.4169 37.4573 32.3537 37.1449L35.4346 36.1461C35.6725 36.0627 35.8786 35.9075 36.0244 35.7018C36.1702 35.4962 36.2486 35.2504 36.2486 34.9983C36.2486 34.7462 36.1702 34.5004 36.0244 34.2947C35.8786 34.0891 35.6725 33.9339 35.4346 33.8505L35.3742 33.8344ZM26.1839 49.0819C25.1123 49.0806 24.0657 48.7579 23.1795 48.1556C22.2935 47.5112 21.6491 46.6614 21.2463 45.6546L20.1992 42.4247C20.0081 41.8996 19.6409 41.457 19.1602 41.1722L10.2477 50.0766C8.92919 51.3977 8.0142 53.0671 7.60981 54.8893L4.07382 70.7972C3.99986 71.1282 4.0107 71.4725 4.10534 71.7982C4.19997 72.1239 4.37533 72.4204 4.61515 72.6602C4.85496 72.9 5.15147 73.0754 5.47715 73.17C5.80283 73.2646 6.14714 73.2755 6.47813 73.2015L22.2894 69.6857C24.1734 69.2704 25.8984 68.3226 27.2592 66.9551L59.4013 34.8171L60.7544 36.1662C61.5095 36.9214 61.9336 37.9456 61.9336 39.0135C61.9336 40.0814 61.5095 41.1056 60.7544 41.8609L56.9688 45.6465C56.5912 46.0247 56.3793 46.5373 56.3796 47.0716C56.38 47.606 56.5927 48.1183 56.9708 48.4959C57.3489 48.8735 57.8615 49.0854 58.3959 49.085C58.9302 49.0846 59.4425 48.872 59.8201 48.4939L63.6018 44.7082C65.1118 43.1977 65.9601 41.1493 65.9601 39.0135C65.9601 36.8777 65.1118 34.8294 63.6018 33.3189L62.2486 31.9657L64.9469 29.2674C67.1506 27.0117 68.3762 23.9782 68.3578 20.8248C68.3395 17.6714 67.0786 14.6524 64.8488 12.4225C62.619 10.1927 59.5999 8.93185 56.4465 8.91349C53.2931 8.89512 50.2596 10.1207 48.0039 12.3244L32.2571 28.0713C32.3484 28.2324 32.4665 28.3787 32.6115 28.5103C32.8934 28.7519 33.1753 28.9533 33.5378 29.0741L36.6026 30.0809C37.7705 30.4837 38.6565 31.1683 39.3009 32.0583C39.905 32.9041 40.2715 33.9512 40.2715 35.0829C40.2715 36.2105 39.9493 37.22 39.3049 38.1114C38.6605 38.9974 37.7745 39.6418 36.7677 40.0043L33.578 41.0514C33.2558 41.1722 32.9337 41.3333 32.692 41.6192C32.4504 41.9052 32.249 42.183 32.1282 42.5455L31.1214 45.6143C30.7186 46.742 30.0743 47.5877 29.1882 48.2321C28.3022 48.8765 27.2632 49.0819 26.1839 49.0819Z" fill="url(#svg1650024675_9765_paint0_linear_144_497)" />
        <g filter="url(#svg1650024675_9765_filter0_f_144_497)">
            <path d="M15.6161 1.88962L17.0177 6.20289C17.4523 7.51482 18.1878 8.70688 19.1654 9.68379C20.143 10.6607 21.3356 11.3954 22.6479 11.8291L26.9652 13.2306L27.0538 13.2547C27.3864 13.3719 27.6746 13.5893 27.8784 13.8771C28.0822 14.165 28.1917 14.509 28.1917 14.8616C28.1917 15.2143 28.0822 15.5583 27.8784 15.8461C27.6746 16.134 27.3864 16.3514 27.0538 16.4686L22.7365 17.8701C21.4235 18.3032 20.2301 19.0376 19.2517 20.0146C18.2734 20.9915 17.5373 22.1839 17.1022 23.4962L15.7047 27.8055C15.5876 28.1382 15.3702 28.4263 15.0823 28.6301C14.7945 28.8339 14.4505 28.9434 14.0978 28.9434C13.7452 28.9434 13.4012 28.8339 13.1133 28.6301C12.8255 28.4263 12.608 28.1382 12.4909 27.8055L11.0894 23.4962C10.6576 22.1794 9.92292 20.9822 8.94439 20.0009C7.96587 19.0196 6.77079 18.2815 5.45519 17.8459L1.1379 16.4444C0.805223 16.3273 0.517097 16.1098 0.313279 15.822C0.109461 15.5342 0 15.1902 0 14.8375C0 14.4848 0.109461 14.1408 0.313279 13.853C0.517097 13.5652 0.805223 13.3477 1.1379 13.2306L5.45519 11.8291C6.75179 11.3842 7.92747 10.6445 8.88976 9.66826C9.85205 8.69199 10.5747 7.50577 11.0008 6.20289L12.4023 1.89365C12.5186 1.55958 12.736 1.26998 13.0243 1.06504C13.3126 0.860108 13.6575 0.75 14.0113 0.75C14.365 0.75 14.7099 0.860108 14.9982 1.06504C15.2865 1.26998 15.5039 1.55958 15.6202 1.89365M35.3742 33.8344L32.2893 32.8356C31.3532 32.5227 30.5028 31.9962 29.8052 31.298C29.1076 30.5997 28.582 29.7487 28.27 28.8123L27.2632 25.7354C27.1798 25.4975 27.0246 25.2914 26.8189 25.1456C26.6133 24.9998 26.3675 24.9215 26.1154 24.9215C25.8633 24.9215 25.6175 24.9998 25.4118 25.1456C25.2062 25.2914 25.051 25.4975 24.9676 25.7354L23.9688 28.8123C23.6625 29.7427 23.1455 30.5898 22.4582 31.2877C21.7708 31.9855 20.9316 32.5153 20.0059 32.8356L16.925 33.8344C16.6871 33.9178 16.481 34.073 16.3352 34.2786C16.1894 34.4843 16.1111 34.7301 16.1111 34.9822C16.1111 35.2343 16.1894 35.4801 16.3352 35.6857C16.481 35.8913 16.6871 36.0466 16.925 36.13L20.0059 37.1328C20.9448 37.4459 21.7977 37.9737 22.4968 38.6742C23.196 39.3747 23.722 40.2287 24.0333 41.1682L25.032 44.245C25.1154 44.4829 25.2707 44.689 25.4763 44.8348C25.6819 44.9807 25.9277 45.059 26.1798 45.059C26.4319 45.059 26.6778 44.9807 26.8834 44.8348C27.089 44.689 27.2442 44.4829 27.3276 44.245L28.3304 41.1682C28.6428 40.2313 29.169 39.3801 29.8673 38.6818C30.5656 37.9835 31.4169 37.4573 32.3537 37.1449L35.4346 36.1461C35.6725 36.0627 35.8786 35.9075 36.0244 35.7018C36.1702 35.4962 36.2486 35.2504 36.2486 34.9983C36.2486 34.7462 36.1702 34.5004 36.0244 34.2947C35.8786 34.0891 35.6725 33.9339 35.4346 33.8505L35.3742 33.8344ZM26.1839 49.0819C25.1123 49.0806 24.0657 48.7579 23.1795 48.1556C22.2935 47.5112 21.6491 46.6614 21.2463 45.6546L20.1992 42.4247C20.0081 41.8996 19.6409 41.457 19.1602 41.1722L10.2477 50.0766C8.92919 51.3977 8.0142 53.0671 7.60981 54.8893L4.07382 70.7972C3.99986 71.1282 4.0107 71.4725 4.10534 71.7982C4.19997 72.1239 4.37533 72.4204 4.61515 72.6602C4.85496 72.9 5.15147 73.0754 5.47715 73.17C5.80283 73.2646 6.14714 73.2755 6.47813 73.2015L22.2894 69.6857C24.1734 69.2704 25.8984 68.3226 27.2592 66.9551L59.4013 34.8171L60.7544 36.1662C61.5095 36.9214 61.9336 37.9456 61.9336 39.0135C61.9336 40.0814 61.5095 41.1056 60.7544 41.8609L56.9688 45.6465C56.5912 46.0247 56.3793 46.5373 56.3796 47.0716C56.38 47.606 56.5927 48.1183 56.9708 48.4959C57.3489 48.8735 57.8615 49.0854 58.3959 49.085C58.9302 49.0846 59.4425 48.872 59.8201 48.4939L63.6018 44.7082C65.1118 43.1977 65.9601 41.1493 65.9601 39.0135C65.9601 36.8777 65.1118 34.8294 63.6018 33.3189L62.2486 31.9657L64.9469 29.2674C67.1506 27.0117 68.3762 23.9782 68.3578 20.8248C68.3395 17.6714 67.0786 14.6524 64.8488 12.4225C62.619 10.1927 59.5999 8.93185 56.4465 8.91349C53.2931 8.89512 50.2596 10.1207 48.0039 12.3244L32.2571 28.0713C32.3484 28.2324 32.4665 28.3787 32.6115 28.5103C32.8934 28.7519 33.1753 28.9533 33.5378 29.0741L36.6026 30.0809C37.7705 30.4837 38.6565 31.1683 39.3009 32.0583C39.905 32.9041 40.2715 33.9512 40.2715 35.0829C40.2715 36.2105 39.9493 37.22 39.3049 38.1114C38.6605 38.9974 37.7745 39.6418 36.7677 40.0043L33.578 41.0514C33.2558 41.1722 32.9337 41.3333 32.692 41.6192C32.4504 41.9052 32.249 42.183 32.1282 42.5455L31.1214 45.6143C30.7186 46.742 30.0743 47.5877 29.1882 48.2321C28.3022 48.8765 27.2632 49.0819 26.1839 49.0819Z" fill="url(#svg1650024675_9765_paint1_linear_144_497)" fill-opacity="0.4" />
        </g>
        <defs>
            <filter id="svg1650024675_9765_filter0_f_144_497" x="-9" y="-8.25" width="86.358" height="90.5" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                <feFlood flood-opacity="0" result="BackgroundImageFix" />
                <feBlend mode="normal" in="SourceGraphic" in2="BackgroundImageFix" result="shape" />
                <feGaussianBlur stdDeviation="4.5" result="effect1_foregroundBlur_144_497" />
            </filter>
            <linearGradient id="svg1650024675_9765_paint0_linear_144_497" x1="1.04657e-06" y1="2.55896" x2="77.0602" y2="4.60924" gradientUnits="userSpaceOnUse">
                <stop offset="0.115" stop-color="#F5F4F2" />
                <stop offset="0.704044" stop-color="#E2CC9D" />
                <stop offset="1" stop-color="#E74C2E" />
            </linearGradient>
            <linearGradient id="svg1650024675_9765_paint1_linear_144_497" x1="1.04657e-06" y1="2.55896" x2="77.0602" y2="4.60924" gradientUnits="userSpaceOnUse">
                <stop offset="0.115" stop-color="#F5F4F2" />
                <stop offset="0.704044" stop-color="#E2CC9D" />
                <stop offset="1" stop-color="#E74C2E" />
            </linearGradient>
        </defs>
    </svg>
    <svg width="199" height="57" viewBox="0 0 199 57" fill="none" id="svg89090024_6903">
        <g clip-path="url(#svg89090024_6903_clip0_144_477)">
            <path d="M141.264 48.8187L144.019 53.5956C144.591 54.6013 145.414 55.3914 146.38 55.9661C149.146 52.4403 151.072 49.7345 152.157 47.8489C153.258 45.9354 154.611 42.9423 156.217 38.8697C151.89 38.2979 148.611 38.012 146.38 38.012C144.239 38.012 140.96 38.2979 136.542 38.8697C136.542 39.9832 136.829 41.0966 137.401 42.1022L141.264 48.8187Z" fill="#0066DA" />
            <path d="M189.163 55.9661C190.129 55.3914 190.952 54.6013 191.524 53.5956L192.669 51.6202L198.142 42.1022C198.714 41.0966 199.001 39.9832 199.001 38.8697C194.558 38.2979 191.285 38.012 189.181 38.012C186.92 38.012 183.647 38.2979 179.362 38.8697C180.948 42.9646 182.284 45.9577 183.368 47.8489C184.462 49.7568 186.394 52.4626 189.163 55.9661Z" fill="#EA4335" />
            <path d="M167.772 18.7563C170.972 14.8747 173.178 11.8816 174.389 9.77705C175.364 8.08242 176.438 5.37669 177.609 1.65985C176.643 1.08518 175.534 0.797852 174.389 0.797852H161.154C160.009 0.797852 158.9 1.1211 157.934 1.65985C159.424 5.92269 160.688 8.95649 161.727 10.7613C162.874 12.7557 164.889 15.4207 167.772 18.7563Z" fill="#00832D" />
            <path d="M179.326 38.8696H156.217L146.38 55.966C147.346 56.5407 148.455 56.828 149.599 56.828H185.944C187.088 56.828 188.197 56.5047 189.163 55.966L179.326 38.8696Z" fill="#2684FC" />
            <path d="M167.772 18.7563L157.934 1.65991C156.968 2.23458 156.146 3.02472 155.573 4.03044L137.401 35.6372C136.829 36.6429 136.542 37.7563 136.542 38.8697H156.217L167.772 18.7563Z" fill="#00AC47" />
            <path d="M189.056 19.8338L179.97 4.03044C179.398 3.02472 178.575 2.23458 177.609 1.65991L167.772 18.7563L179.326 38.8697H198.965C198.965 37.7563 198.679 36.6429 198.106 35.6372L189.056 19.8338Z" fill="#FFBA00" />
        </g>
        <g clip-path="url(#svg89090024_6903_clip1_144_477)">
            <path d="M69.0211 3.21323L100.23 0.925136C104.064 0.598532 105.049 0.818757 107.459 2.55815L117.421 9.52507C119.065 10.7232 119.612 11.0498 119.612 12.3544V50.5633C119.612 52.9577 118.736 54.3742 115.669 54.5907L79.4273 56.7687C77.1261 56.877 76.03 56.5504 74.8249 55.0256L67.4889 45.556C66.1728 43.8128 65.6276 42.5083 65.6276 40.9835V7.02049C65.6276 5.06273 66.5037 3.42972 69.0211 3.21323Z" fill="white" />
            <path d="M100.23 0.925136L69.0211 3.21323C66.5037 3.42972 65.6276 5.06273 65.6276 7.02049V40.9835C65.6276 42.5083 66.1728 43.8128 67.4889 45.556L74.8249 55.0256C76.03 56.5504 77.1261 56.877 79.4273 56.7687L115.669 54.5907C118.734 54.3742 119.612 52.9577 119.612 50.5633V12.3544C119.612 11.117 119.119 10.7606 117.67 9.70423C117.586 9.64459 117.503 9.58487 117.42 9.52507L107.459 2.55815C105.049 0.818757 104.064 0.598532 100.23 0.925136ZM80.247 11.7292C77.2878 11.927 76.6166 11.9718 74.9358 10.615L70.6624 7.24071C70.2281 6.804 70.4462 6.25904 71.5404 6.15079L101.542 3.97468C104.062 3.75632 105.374 4.62788 106.359 5.38934L111.505 9.09022C111.725 9.20033 112.272 9.85167 111.614 9.85167L80.6305 11.703L80.247 11.7292ZM76.7971 50.2366V17.8003C76.7971 16.3837 77.2351 15.7305 78.5474 15.6204L114.133 13.5526C115.34 13.4443 115.885 14.2058 115.885 15.6204V47.8403C115.885 49.2568 115.665 50.455 113.695 50.5633L79.6416 52.5229C77.6713 52.6311 76.7971 51.9798 76.7971 50.2366ZM110.413 19.5397C110.631 20.5195 110.413 21.4993 109.425 21.6113L107.784 21.9341V45.8826C106.359 46.644 105.047 47.0789 103.951 47.0789C102.199 47.0789 101.76 46.5339 100.448 44.9027L89.715 28.1396V44.3578L93.1103 45.1211C93.1103 45.1211 93.1103 47.0807 90.3711 47.0807L82.8189 47.5156C82.5989 47.0789 82.8189 45.9908 83.5841 45.7743L85.5563 45.2312V23.7874L82.8189 23.5671C82.5989 22.5873 83.146 21.1727 84.6802 21.0626L92.7832 20.5213L103.951 37.501V22.4791L101.104 22.1544C100.884 20.9543 101.76 20.0828 102.855 19.9764L110.413 19.5397Z" fill="black" />
        </g>
        <g clip-path="url(#svg89090024_6903_clip2_144_477)">
            <path d="M15.2426 13.4032C15.2426 13.9615 15.1953 14.8918 14.6276 15.4966C14.0126 16.0547 13.0664 16.1013 12.4987 16.1013H6.44346C4.69311 16.1013 3.65229 16.1013 2.94276 16.1942C2.56436 16.2407 2.09129 16.4269 1.85468 16.5198C1.76016 16.5663 1.76016 16.5198 1.80742 16.4732L15.6212 2.65856C15.6686 2.61209 15.7157 2.61209 15.6686 2.70518C15.5737 2.93767 15.3847 3.40296 15.3373 3.77501C15.2428 4.4728 15.2428 5.49602 15.2428 7.21702L15.2426 13.4032ZM28.1102 55.1729C26.5018 54.1497 25.6503 52.8007 25.3192 51.9634C24.988 51.1727 24.7988 50.289 24.7988 49.405C24.7988 45.5444 28.0157 42.3815 31.9894 42.3815C32.2693 42.3803 32.5467 42.4337 32.8055 42.5385C33.0643 42.6432 33.2994 42.7974 33.4973 42.992C33.6952 43.1865 33.852 43.4177 33.9585 43.6722C34.0651 43.9267 34.1194 44.1993 34.1182 44.4745C34.1182 45.2653 33.6924 45.9166 33.0301 46.2885C32.7936 46.428 32.4625 46.5211 32.2259 46.5677C31.9893 46.6141 31.0906 46.7073 30.6647 47.0793C30.1917 47.4514 29.8131 48.056 29.8131 48.7074C29.8131 49.4049 30.097 50.0561 30.5701 50.5214C31.4215 51.3587 32.557 51.8237 33.7869 51.8237C37.0037 51.8237 39.6058 49.2653 39.6058 46.1025C39.6058 43.2651 37.6663 40.7533 35.1117 39.637C34.7333 39.4509 34.1182 39.3114 33.5506 39.172C32.8409 39.0323 32.1786 38.9393 32.1312 38.9393C30.1444 38.7067 25.1769 37.1719 24.846 32.846C24.846 32.846 23.3795 39.358 20.4464 41.1255C20.1627 41.265 19.7841 41.4044 19.3583 41.4977C18.9326 41.5906 18.4596 41.6371 18.3176 41.6371C13.5397 41.9162 8.47783 40.4277 4.97698 36.8927C4.97698 36.8927 2.61163 34.9856 1.38161 29.6365C1.09774 28.3342 0.529995 26.0085 0.19886 23.8224C0.056924 23.0316 0.00966188 22.4268 -0.0377502 21.8689C-0.0377502 19.5896 1.38161 18.0546 3.17907 17.822H12.8297C14.4855 17.822 15.4317 17.4033 16.0465 16.8453C16.8507 16.101 17.0401 15.0312 17.0401 13.7753V3.96074C17.2765 2.23974 18.8375 0.797852 21.1559 0.797852H22.2913C22.7643 0.797852 23.3321 0.844321 23.8524 0.890791C24.231 0.937408 24.5621 1.0305 25.1297 1.1699C28.0154 1.86769 28.6304 4.75147 28.6304 4.75147C28.6304 4.75147 34.0708 5.68175 36.8145 6.14703C39.4165 6.61217 45.8503 7.0307 47.0801 13.4032C49.966 28.5671 48.2156 43.2648 48.0735 43.2648C46.0393 57.591 33.9288 56.8933 33.9288 56.8933C31.3269 56.8933 29.3873 56.056 28.1099 55.1722M38.9907 24.7057C37.4295 24.5662 36.1048 25.171 35.6319 26.3336C35.5372 26.5663 35.4426 26.8455 35.49 26.9849C35.5372 27.1245 35.6319 27.171 35.7266 27.2174C36.2942 27.4966 37.2403 27.6363 38.6123 27.7757C39.9842 27.9152 40.9304 28.0082 41.5454 27.9152C41.6399 27.9152 41.7344 27.8686 41.8293 27.7292C41.9238 27.5896 41.8765 27.3105 41.8765 27.078C41.6873 25.7754 40.552 24.8917 38.9908 24.7057" fill="#00A82D" />
        </g>
        <defs>
            <clipPath id="svg89090024_6903_clip0_144_477">
                <rect width="62.4576" height="56.0962" fill="white" transform="translate(136.542 0.797852)" />
            </clipPath>
            <clipPath id="svg89090024_6903_clip1_144_477">
                <rect width="53.984" height="56.1011" fill="white" transform="translate(65.6276 0.797852)" />
            </clipPath>
            <clipPath id="svg89090024_6903_clip2_144_477">
                <rect width="48.6915" height="56.1011" fill="white" transform="translate(0 0.797852)" />
            </clipPath>
        </defs>
    </svg>
    <svg width="237" height="17" viewBox="0 0 237 17" fill="none" id="svg-1415167899_999">
        <path fill-rule="evenodd" clip-rule="evenodd" d="M237 9H0V8H237V9ZM118.004 1.29644L109.88 8H126.129L118.004 1.29644ZM127.7 8L118.004 0L108.308 8L107.096 9H108.668H127.341H128.912L127.7 8ZM108.76 15.7036L116.885 9L100.636 9L108.76 15.7036ZM99.0643 9L108.76 17L118.456 9L119.668 8H118.097L99.4236 8H97.8523L99.0643 9ZM108.76 1.29644L100.636 8H116.885L108.76 1.29644ZM118.456 8L108.76 0L99.0643 8L97.8523 9H99.4236H118.097H119.668L118.456 8ZM118.004 15.7036L126.129 9L109.88 9L118.004 15.7036ZM108.308 9L118.004 17L127.7 9L128.912 8H127.341L108.668 8H107.096L108.308 9Z" fill="url(#svg-1415167899_999_paint0_linear_72_237)" />
        <defs>
            <linearGradient id="svg-1415167899_999_paint0_linear_72_237" x1="259.201" y1="27.7111" x2="34.499" y2="-79.1077" gradientUnits="userSpaceOnUse">
                <stop offset="0.115" stop-color="#F5F4F2" />
                <stop offset="0.704044" stop-color="#E2CC9D" />
                <stop offset="1" stop-color="#E74C2E" />
            </linearGradient>
        </defs>
    </svg>
    <svg width="18" height="16" viewBox="0 0 18 16" fill="none" id="svg-1643614698_589">
        <path d="M-1.80526e-07 6.85714L-2.80438e-07 9.14286L13.5 9.14286L13.5 11.4286L15.75 11.4286L15.75 9.14286L18 9.14286L18 6.85714L15.75 6.85714L15.75 4.57143L13.5 4.57143L13.5 6.85714L-1.80526e-07 6.85714ZM11.25 2.28571L13.5 2.28571L13.5 4.57143L11.25 4.57143L11.25 2.28571ZM11.25 2.28571L9 2.28571L9 -3.93402e-07L11.25 -2.95052e-07L11.25 2.28571ZM11.25 13.7143L13.5 13.7143L13.5 11.4286L11.25 11.4286L11.25 13.7143ZM11.25 13.7143L9 13.7143L9 16L11.25 16L11.25 13.7143Z" fill="#E74C2E" />
    </svg>
</div>
