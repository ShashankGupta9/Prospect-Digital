/**
 * Prospect Digital — Hero Background Animation: Strange Tubes #2
 * Based on Strange Tubes #2 by soju22 (Kevin Levron)
 * Optimized for Three.js r128+, smooth 60fps, responsive resize,
 * and IntersectionObserver pausing.
 */
(function () {
  'use strict';

  var container = document.getElementById('heroBgContainer');
  var canvas = document.getElementById('heroCanvas');
  if (!container || !canvas) return;

  // Compact, fast 2D Simplex Noise
  function SimplexNoise2D() {
    var F2 = 0.5 * (Math.sqrt(3.0) - 1.0);
    var G2 = (3.0 - Math.sqrt(3.0)) / 6.0;
    var p = new Uint8Array(256);
    for (var i = 0; i < 256; i++) p[i] = Math.floor(Math.random() * 256);
    var perm = new Uint8Array(512);
    var permMod12 = new Uint8Array(512);
    for (var j = 0; j < 512; j++) {
      perm[j] = p[j & 255];
      permMod12[j] = perm[j] % 12;
    }
    var grad2 = [
      [1, 1], [-1, 1], [1, -1], [-1, -1],
      [1, 0], [-1, 0], [0, 1], [0, -1],
      [1, 1], [-1, 1], [1, -1], [-1, -1]
    ];

    this.noise2D = function (xin, yin) {
      var s = (xin + yin) * F2;
      var i = Math.floor(xin + s);
      var j = Math.floor(yin + s);
      var t = (i + j) * G2;
      var x0 = xin - (i - t);
      var y0 = yin - (j - t);

      var i1, j1;
      if (x0 > y0) { i1 = 1; j1 = 0; }
      else { i1 = 0; j1 = 1; }

      var x1 = x0 - i1 + G2;
      var y1 = y0 - j1 + G2;
      var x2 = x0 - 1.0 + 2.0 * G2;
      var y2 = y0 - 1.0 + 2.0 * G2;

      var ii = i & 255;
      var jj = j & 255;
      var gi0 = permMod12[ii + perm[jj]];
      var gi1 = permMod12[ii + i1 + perm[jj + j1]];
      var gi2 = permMod12[ii + 1 + perm[jj + 1]];

      var n0 = 0.0, n1 = 0.0, n2 = 0.0;
      var t0 = 0.5 - x0 * x0 - y0 * y0;
      if (t0 >= 0) {
        t0 *= t0;
        n0 = t0 * t0 * (grad2[gi0][0] * x0 + grad2[gi0][1] * y0);
      }
      var t1 = 0.5 - x1 * x1 - y1 * y1;
      if (t1 >= 0) {
        t1 *= t1;
        n1 = t1 * t1 * (grad2[gi1][0] * x1 + grad2[gi1][1] * y1);
      }
      var t2 = 0.5 - x2 * x2 - y2 * y2;
      if (t2 >= 0) {
        t2 *= t2;
        n2 = t2 * t2 * (grad2[gi2][0] * x2 + grad2[gi2][1] * y2);
      }

      return 70.0 * (n0 + n1 + n2);
    };
  }

  function startStrangeTubes() {
    if (typeof THREE === 'undefined') return;

    var simplex = new SimplexNoise2D();

    var conf = {
      fov: 75,
      cameraZ: 140,
      tubeRadius: 2.8,
      resY: 13,
      resX: 5.0,
      noiseCoef: 50,
      timeCoef: 48,
      heightCoef: 24,
      ambientColor: 0x162412,
      lightIntensity: 2.0,
      light1Color: 0x24f59e, // neon emerald
      light2Color: 0x7db82a, // prospect green
      light3Color: 0x00e5ff, // cyber cyan
      light4Color: 0xa3e635  // bright lime
    };

    var renderer, scene, camera;
    var width, height, wWidth, wHeight;
    var light1, light2, light3, light4;
    var objects = [];
    var noiseConf = {};
    var animId = null;
    var isRunning = false;
    var heroEl = document.getElementById('hero');

    var mouse = { x: 0, y: 0, targetX: 0, targetY: 0 };

    var PALETTES = [
      [0x24f59e, 0x10b981, 0x7db82a, 0x059669, 0xa3e635, 0x34d399, 0x00e5ff],
      [0x10b981, 0x06b6d4, 0x34d399, 0x0284c7, 0x6ee7b7, 0x0f766e, 0x22d3ee],
      [0x7db82a, 0xe8b020, 0x22c55e, 0xf59e0b, 0x84cc16, 0x15803d, 0x4ade80],
      [0x00f5d4, 0x7b2cbf, 0x00bbf9, 0x9b5de5, 0x2ec4b6, 0x3a86ff, 0x57cc99]
    ];
    var currentPaletteIdx = 0;

    function getRandomColor() {
      var pal = PALETTES[currentPaletteIdx];
      return pal[Math.floor(Math.random() * pal.length)];
    }

    // Modern ES6 class extending THREE.Curve (compatible with Three.js r128+)
    class CustomCurve extends THREE.Curve {
      constructor(x, y, l, noise) {
        super();
        this.x = x;
        this.y = y;
        this.l = l;
        this.noise = noise;
        this.yn = this.y * this.noise.coef;
      }
      getPoint(t, optionalTarget) {
        var point = optionalTarget || new THREE.Vector3();
        var x = this.x + t * this.l;
        var xn = x * this.noise.coef;
        var noise1 = simplex.noise2D(
          xn + this.noise.time + this.noise.mouseX * 0.5,
          this.yn - this.noise.time + this.noise.mouseY * 0.5
        );
        var noise2 = simplex.noise2D(
          this.yn + this.noise.time,
          xn - this.noise.time
        );
        var z = noise2 * this.noise.height;
        var y = this.y + noise1 * this.noise.height;
        return point.set(x, y, z);
      }
    }

    // Tube class
    class Tube {
      constructor(x, y, l, segments, radius, color, noise) {
        this.segments = Math.max(16, segments);
        this.radialSegments = 8;
        this.radius = radius;
        this.curve = new CustomCurve(x, y, l, noise);
        this.geometry = new THREE.TubeGeometry(this.curve, this.segments, radius, this.radialSegments, false);
        this.material = new THREE.MeshStandardMaterial({
          color: color,
          metalness: 0.90,
          roughness: 0.18
        });
        this.mesh = new THREE.Mesh(this.geometry, this.material);
      }

      update() {
        this.frames = this.curve.computeFrenetFrames(this.segments, false);
        if (!this.frames || !this.frames.normals) return;

        var posAttr = this.geometry.attributes.position;
        var normAttr = this.geometry.attributes.normal;
        var pArray = posAttr.array;
        var nArray = normAttr.array;
        var P = new THREE.Vector3();
        var normal = new THREE.Vector3();

        for (var i = 0; i <= this.segments; i++) {
          P = this.curve.getPointAt(i / this.segments, P);
          var N = this.frames.normals[i] || this.frames.normals[this.segments - 1];
          var B = this.frames.binormals[i] || this.frames.binormals[this.segments - 1];

          for (var j = 0; j <= this.radialSegments; j++) {
            var v = (j / this.radialSegments) * Math.PI * 2;
            var sin = Math.sin(v);
            var cos = -Math.cos(v);

            normal.x = cos * N.x + sin * B.x;
            normal.y = cos * N.y + sin * B.y;
            normal.z = cos * N.z + sin * B.z;
            normal.normalize();

            var index = (i * (this.radialSegments + 1) + j) * 3;
            nArray[index] = normal.x;
            nArray[index + 1] = normal.y;
            nArray[index + 2] = normal.z;

            pArray[index] = P.x + this.radius * normal.x;
            pArray[index + 1] = P.y + this.radius * normal.y;
            pArray[index + 2] = P.z + this.radius * normal.z;
          }
        }

        posAttr.needsUpdate = true;
        normAttr.needsUpdate = true;
      }
    }

    function init() {
      renderer = new THREE.WebGLRenderer({
        canvas: canvas,
        alpha: true,
        antialias: true,
        powerPreference: 'high-performance'
      });
      renderer.setPixelRatio(Math.min(window.devicePixelRatio || 1, 1.5));

      camera = new THREE.PerspectiveCamera(conf.fov, 1, 0.1, 1000);
      camera.position.z = conf.cameraZ;

      scene = new THREE.Scene();

      updateSize();
      window.addEventListener('resize', updateSize, { passive: true });

      window.addEventListener('mousemove', function (e) {
        var rect = container.getBoundingClientRect();
        mouse.targetX = ((e.clientX - rect.left) / width) * 2 - 1;
        mouse.targetY = -(((e.clientY - rect.top) / height) * 2 - 1);
      }, { passive: true });

      document.body.addEventListener('click', function (e) {
        if (e.target.closest('a, button, input, textarea, select, [role="button"]')) return;
        currentPaletteIdx = (currentPaletteIdx + 1) % PALETTES.length;
        updateColors();
      });

      initLights();
      initObjects();

      // Intersection Observer for zero idle CPU load
      if ('IntersectionObserver' in window && heroEl) {
        var observer = new IntersectionObserver(function (entries) {
          entries.forEach(function (entry) {
            if (entry.isIntersecting) {
              if (!isRunning) {
                isRunning = true;
                updateSize();
                animate();
              }
            } else {
              isRunning = false;
              if (animId) {
                cancelAnimationFrame(animId);
                animId = null;
              }
            }
          });
        }, { threshold: 0.05 });
        observer.observe(heroEl);
      } else {
        isRunning = true;
        animate();
      }
    }

    function initLights() {
      scene.add(new THREE.AmbientLight(conf.ambientColor, 1.6));

      var z = 55;
      var lightDistance = 600;
      light1 = new THREE.PointLight(conf.light1Color, conf.lightIntensity, lightDistance);
      light1.position.set(0, wHeight / 2, z);
      scene.add(light1);

      light2 = new THREE.PointLight(conf.light2Color, conf.lightIntensity, lightDistance);
      light2.position.set(0, -wHeight / 2, z);
      scene.add(light2);

      light3 = new THREE.PointLight(conf.light3Color, conf.lightIntensity, lightDistance);
      light3.position.set(wWidth / 2, 0, z);
      scene.add(light3);

      light4 = new THREE.PointLight(conf.light4Color, conf.lightIntensity, lightDistance);
      light4.position.set(-wWidth / 2, 0, z);
      scene.add(light4);
    }

    function initObjects() {
      updateNoise();
      var nx = Math.round(wWidth / conf.resX) + 1;
      var ny = Math.round(wHeight / conf.resY) + 1;

      for (var k = 0; k < objects.length; k++) {
        scene.remove(objects[k].mesh);
        objects[k].geometry.dispose();
        objects[k].material.dispose();
      }
      objects = [];

      for (var j = 0; j < ny; j++) {
        var color = getRandomColor();
        var yPos = -wHeight / 2 + j * conf.resY;
        var tube = new Tube(-wWidth / 2, yPos, wWidth, nx, conf.tubeRadius, color, noiseConf);
        objects.push(tube);
        scene.add(tube.mesh);
      }
    }

    function updateColors() {
      for (var i = 0; i < objects.length; i++) {
        objects[i].material.color.setHex(getRandomColor());
      }
      var pal = PALETTES[currentPaletteIdx];
      light1.color.setHex(pal[0]);
      light2.color.setHex(pal[1 % pal.length]);
      light3.color.setHex(pal[2 % pal.length]);
      light4.color.setHex(pal[3 % pal.length]);
    }

    function updateNoise() {
      noiseConf.coef = conf.noiseCoef * 0.00012;
      noiseConf.height = conf.heightCoef;
      noiseConf.time = Date.now() * conf.timeCoef * 0.000002;
      noiseConf.mouseX = mouse.x / 2;
      noiseConf.mouseY = mouse.y / 2;
      noiseConf.mouse = mouse.x + mouse.y;
    }

    function updateSize() {
      width = container.clientWidth || window.innerWidth;
      height = container.clientHeight || window.innerHeight;

      if (renderer && camera) {
        renderer.setSize(width, height);
        camera.aspect = width / height;
        camera.updateProjectionMatrix();

        var vFOV = (camera.fov * Math.PI) / 180;
        var h = 2 * Math.tan(vFOV / 2) * Math.abs(conf.cameraZ);
        var w = h * camera.aspect;
        wWidth = w;
        wHeight = h;
      }
    }

    function animate() {
      if (!isRunning) return;
      animId = requestAnimationFrame(animate);

      // Smooth mouse interpolation
      mouse.x += (mouse.targetX - mouse.x) * 0.08;
      mouse.y += (mouse.targetY - mouse.y) * 0.08;

      // Parallax camera rotation
      camera.position.x = mouse.x * 12;
      camera.position.y = mouse.y * 8;
      camera.lookAt(0, 0, 0);

      animateObjects();
      animateLights();

      renderer.render(scene, camera);
    }

    function animateObjects() {
      updateNoise();
      for (var i = 0; i < objects.length; i++) {
        objects[i].update();
      }
    }

    function animateLights() {
      var time = Date.now() * 0.001;
      var dx = wWidth / 2;
      var dy = wHeight / 2;
      light1.position.x = Math.sin(time * 0.1) * dx;
      light1.position.y = Math.cos(time * 0.2) * dy;
      light2.position.x = Math.cos(time * 0.3) * dx;
      light2.position.y = Math.sin(time * 0.4) * dy;
      light3.position.x = Math.sin(time * 0.5) * dx;
      light3.position.y = Math.sin(time * 0.6) * dy;
      light4.position.x = Math.sin(time * 0.7) * dx;
      light4.position.y = Math.cos(time * 0.8) * dy;
    }

    init();
  }

  // Check Three.js availability or wait
  if (typeof THREE !== 'undefined') {
    startStrangeTubes();
  } else {
    window.addEventListener('load', function () {
      if (typeof THREE !== 'undefined') {
        startStrangeTubes();
      }
    });
  }
})();
