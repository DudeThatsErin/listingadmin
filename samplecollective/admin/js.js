/**
 * We could - theoretically - create this in our jQuery file, but
 * as this can be constructed through plain ole JavaScript, why
 * the hell not? :D Wrapping this in an object, o' course!
 */
const tess = {
    clean: function (e) {
        let pattern, execute, string, q = '';

        /**
         * Do main clean; if nothing is cleaned, return original string
         */
        pattern = /([[\.\+\?\:\;\*\(\)\/\]\[#'"!_-]+)/g;
        execute = e.replace(pattern, '');
        string = execute == '' || execute == null ? e : execute;

        /**
         * Strip white space \o/
         */
        o = /([\s]+)/g;
        p = string.replace(o, '');
        q = this.trim(p);
        return q.toLowerCase();
    },

    getQuery: function () {
		let s = window.location.search, n = '', r = '', m = '', h = '', p = '',
			c = '', a = '';

		n = s.split('=');
        m = /&([A-Za-z0-9=?]+)/;
        e = m.exec(n[1]);

        /**
         * Is there a & character in our query? If so, let's do some regex-replacing
         * jazz~
         */
        if (e == null) {
            r = n[1];
        } else {
            h = s;
            p = /(\?[a-z=]+)(&[a-z0-9=]+)/;
            c = h.replace(p, "$1");
            a = c.split('=');
            r = a[1];
        }

        return r;
    },

    getURL: function () {
        const url = window.location.href;
        let u = url, r, e, n = '';

        r = /[a-zA-Z]+.php/;
        e = r.exec(u);
        if(e == null || e.length === 0) {
        	return '';
		}
        n = e[0];
        return n;
    },

    inArray: function (array, variable) {
        const n = array.length;
        let v = 0;
        for (let i = 0; i < n; i++) {
            if (n[i] == variable) {
                v = 1;
            }
        }

        return v;
    },

    joinedID: function (text) {
        let m = '', c = '';
        m = /([0-9]+)$/;
        c = m.exec(text);
        return c[0];
    },

    trim: function (string) {
        return string.replace(/^\s*|\s*$/g, '');
    }
};
