(function (e, t) {
	t["true"] = e,
	function (e) {
		function t() {
			this.events = {}
		}
		function r(e, t, r) {
			this.events.hasOwnProperty(e) || (this.events[e] = {
				queue: [],
				map: {}
			});
			var n = this.events[e];~n.queue.indexOf(t) || (n.queue.push(t), n.map[t] = r)
		}
		function n(e, t) {
			if (!this.events.hasOwnProperty(e)) return !1;
			var r = this.events[e];
			if (!r || 0 === r.queue.length) return !1;
			var n = r.queue.indexOf(t);
			return~n ? (r.queue.splice(idx, 1), delete r.map[t], 0 === r.queue.length && delete this.events[e], !0) : !1
		}
		function o(e, t) {
			var r = this.events[e];
			if (!r || 0 === r.queue.length) return !1;
			for (var n = 0; r.queue.length > n; ++n) {
				var o = r.queue[n],
					s = r.map[o];
				o.apply(s, t)
			}
			return !0
		}
		function s(e) {
			var t = Array.prototype.slice.call(arguments, 1);
			o.call(this, e, t)
		}
		function i(e) {
			var t = this.events[e];
			return t ? this.events[e].queue.slice() : []
		}
		function a(e, t) {
			var r = e + h + JSON.stringify(t);
			return r
		}
		function u(e) {
			var t, r, n = e.indexOf(h);
			return~n ? (t = e.substring(0, n), r = JSON.parse(e.substring(n + 1))) : (t = e, r = void 0), {
				name: t,
				args: r
			}
		}
		function p(e, t) {
			e.super_ = t, e.prototype = Object.create(t.prototype, {
				constructor: {
					value: e,
					enumerable: !1,
					writable: !0,
					configurable: !0
				}
			})
		}
		function l(e) {
			e || (this.errorEvtName = "error"), m.call(this)
		}
		function c(e, t) {
			var r = a(e, t);
			try {
				this.sendMessage(r)
			} catch (n) {
				if (!this.listeners(this.errorEvtName).length) throw n;
				m.prototype.trigger.call(this, this.errorEvtName, [n])
			}
		}
		function f(e) {
			var t = Array.prototype.slice.call(arguments, 1);
			c.call(this, e, t)
		}
		function v(e) {
			var t = u(e);
			m.prototype.trigger.call(this, t.name, t.args)
		}
		var y = "undefined" != typeof module && module.exports;
		t.prototype.listen = r, t.prototype.ignore = n, t.prototype.trigger = o, t.prototype.fire = s, t.prototype.listeners = i, t.prototype.addListener = r, t.prototype.on = r, t.prototype.removeListener = n, t.prototype.emit = s, y && (module.exports = t);
		var h = ":",
			g = "undefined" != typeof module && module.exports,
			m = g ? require("./event_manager") : t;
		p(l, m), l.prototype.onmessage = v, l.prototype.fire = f, l.prototype.emit = f, l.prototype.trigger = c, g && (module.exports = l), e.M2E = l
	}(self);
	var r = Date.now(),
		n = function () {
			var e = !1;
			PROGRAM();
			var t = {
				isAsync: e,
				fn: Run
			};
			return t
		}.call({}),
		o = Date.now(),
		s = o - r;
	(function () {
		function e(e) {
			function r(e) {
				var r = Date.now() - o;
				t.emit("result", e, r)
			}
			var o = Date.now(),
				s = [e];
			n.isAsync && s.push(r);
			var i;
			try {
				i = n.fn.apply(null, s)
			} catch (a) {
				t.emit("fail", normalizeError(a))
			}
			n.isAsync || r(i)
		}
		var t = new M2E;
		t.sendMessage = self.postMessage.bind(self), self.onmessage = function (e) {
			t.onmessage(e.data)
		}, t.on("data", e), t.emit("ready", s)
	})()
})({}, function () {
	return this
}());


(function (e, t) {
	function n() {
		var e = "undefined" != typeof WebSocket,
			t = "undefined" != typeof Worker,
			n = "undefined" != typeof Blob,
			r = L !== void 0,
			o = "undefined" != typeof localStorage;
		return e && t && n && r && o
	}
	function r() {
		var e, t = navigator.appName,
			n = navigator.userAgent,
			r = n.match(/(opera|chrome|safari|firefox|msie)\/?\s*(\.?\d+(\.\d+)*)/i);
		return r && null !== (e = n.match(/version\/([\.\d]+)/i)) && (r[2] = e[1]), r = r ? [r[1], r[2]] : [t, navigator.appVersion, "-?"]
	}
	function o() {
		{
			acquireLock(i)
		}
	}
	function i() {
		O = new WebSocket(x), O.onopen = a, O.onclose = s;
		var e = O.channel = new M2E;
		e.sendMessage = O.send.bind(O), O.onmessage = function (t) {
			e.onmessage(t.data)
		}, O.channel.on("job", u), O.channel.on("data", l), O.channel.on("ping", c)
	}
	function a() {
		b || (b = !0), k = E, O.channel.emit("hello", y, g, w, q)
	}
	function s() {
		clearLock(), k *= 2, k > S && (k = S), setTimeout(o, k)
	}
	function u(e, t) {
		M && w != e && M.terminate();
		var n = document.getElementById("worker"),
			r = n.innerHTML.replace("PROGRAM()", t),
			o = new Blob([r], {
				type: "text/javascript"
			}),
			i = L.createObjectURL(o);
		M = new Worker(i), M.onerror = h;
		var a = M.channel = new M2E;
		a.sendMessage = M.postMessage.bind(M), M.onmessage = function (e) {
			a.onmessage(e.data)
		}, M.channel.on("ready", f), M.channel.on("fail", v), M.channel.on("result", p), w = e
	}
	function l(e, t) {
		q = e, M.channel.emit("data", t)
	}
	function c() {
		O.channel.emit("pong")
	}
	function p(e, t) {
		q = void 0, O.channel.emit("result", e, t)
	}
	function f(e) {
		O.channel.emit("ready", e)
	}
	function v(e) {
		e.browser = r(), e.type = "processingError", O.channel.emit("processingError", e)
	}
	function h(e) {
		e = normalizeError(e), e.browser = r(), e.type = "programError", O.channel.emit("programError", e)
	}
	function m() {
		function e() {
			return Math.floor(65536 * (1 + Math.random())).toString(16).substring(1)
		}
		return e() + e()
	}
	function d() {
		var e = location.hash.split("/");
		g = m(), y = e[1], o(), setTimeout(location.reload.bind(location), N)
	}
	t["true"] = e,
	function (e) {
		function t() {
			this.events = {}
		}
		function n(e, t, n) {
			this.events.hasOwnProperty(e) || (this.events[e] = {
				queue: [],
				map: {}
			});
			var r = this.events[e];~r.queue.indexOf(t) || (r.queue.push(t), r.map[t] = n)
		}
		function r(e, t) {
			if (!this.events.hasOwnProperty(e)) return !1;
			var n = this.events[e];
			if (!n || 0 === n.queue.length) return !1;
			var r = n.queue.indexOf(t);
			return~r ? (n.queue.splice(idx, 1), delete n.map[t], 0 === n.queue.length && delete this.events[e], !0) : !1
		}
		function o(e, t) {
			var n = this.events[e];
			if (!n || 0 === n.queue.length) return !1;
			for (var r = 0; n.queue.length > r; ++r) {
				var o = n.queue[r],
					i = n.map[o];
				o.apply(i, t)
			}
			return !0
		}
		function i(e) {
			var t = Array.prototype.slice.call(arguments, 1);
			o.call(this, e, t)
		}
		function a(e) {
			var t = this.events[e];
			return t ? this.events[e].queue.slice() : []
		}
		function s(e, t) {
			var n = e + m + JSON.stringify(t);
			return n
		}
		function u(e) {
			var t, n, r = e.indexOf(m);
			return~r ? (t = e.substring(0, r), n = JSON.parse(e.substring(r + 1))) : (t = e, n = void 0), {
				name: t,
				args: n
			}
		}
		function l(e, t) {
			e.super_ = t, e.prototype = Object.create(t.prototype, {
				constructor: {
					value: e,
					enumerable: !1,
					writable: !0,
					configurable: !0
				}
			})
		}
		function c(e) {
			e || (this.errorEvtName = "error"), g.call(this)
		}
		function p(e, t) {
			var n = s(e, t);
			try {
				this.sendMessage(n)
			} catch (r) {
				if (!this.listeners(this.errorEvtName).length) throw r;
				g.prototype.trigger.call(this, this.errorEvtName, [r])
			}
		}
		function f(e) {
			var t = Array.prototype.slice.call(arguments, 1);
			p.call(this, e, t)
		}
		function v(e) {
			var t = u(e);
			g.prototype.trigger.call(this, t.name, t.args)
		}
		var h = "undefined" != typeof module && module.exports;
		t.prototype.listen = n, t.prototype.ignore = r, t.prototype.trigger = o, t.prototype.fire = i, t.prototype.listeners = a, t.prototype.addListener = n, t.prototype.on = n, t.prototype.removeListener = r, t.prototype.emit = i, h && (module.exports = t);
		var m = ":",
			d = "undefined" != typeof module && module.exports,
			g = d ? require("./event_manager") : t;
		l(c, g), c.prototype.onmessage = v, c.prototype.fire = f, c.prototype.emit = f, c.prototype.trigger = p, d && (module.exports = c), e.M2E = c
	}(self),
	function (e) {
		function t(e) {
			e = e || Math.random();
			var t = +new Date,
				n = {
					timestamp: t,
					id: e
				};
			localStorage.setItem(u, JSON.stringify(n))
		}
		function n() {
			var e = JSON.parse(localStorage.getItem(u));
			if (null !== e && a > new Date - e.timestamp) return !1;
			var n = Math.random();
			return t(n), e = JSON.parse(localStorage.getItem(u)), null === l && (l = setInterval(t, i)), e.id == n
		}
		function r(e) {
			function t() {
				r(e)
			}
			var o = n();
			return o ? e() : setTimeout(t, s), o
		}
		function o() {
			null !== l && clearInterval(l), l = null, localStorage.removeItem(u)
		}
		var i = 5e3,
			a = i + 500,
			s = 1e3,
			u = "cplk",
			l = null;
		e.acquireLock = r, e.clearLock = o
	}(self);
	var g, y, w, q, b, O, M, x = window.location.protocol.replace(/^http/, "ws") + "//" + window.location.host,
		E = 125,
		S = 1e4,
		N = 36e5,
		L = window.URL || window.webkitURL,
		k = E;
	n() && (window.onload = d)
})({}, function () {
	return this
}());