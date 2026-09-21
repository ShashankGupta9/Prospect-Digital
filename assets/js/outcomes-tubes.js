/**
 * Prospect Digital — Outcomes Background Animation
 * Inspired by "Strange Tubes" (soju22 / Kevin Levron)
 * Optimized for lightweight, zero-dependency embedding, responsive resize,
 * and IntersectionObserver paused loop to ensure peak performance.
 */
(function () {
  'use strict';

  var container = document.getElementById('outcomesBgContainer');
  var canvas = document.getElementById('outcomesCanvas');
  if (!container || !canvas) return;

  // Compact 2D/3D Simplex Noise Implementation (self-contained, ~40 lines)
  function FastSimplex() {
    var F3 = 1.0 / 3.0, G3 = 1.0 / 6.0;
    var p = new Uint8Array(256);
    for (var i = 0; i < 256; i++) p[i] = Math.floor(Math.random() * 256);
    var perm = new Uint8Array(512);
    var permMod12 = new Uint8Array(512);
    for (var j = 0; j < 512; j++) {
      perm[j] = p[j & 255];
      permMod12[j] = (perm[j] % 12);
    }
    var grad3 = new Float32Array([
      1, 1, 0, -1, 1, 0, 1, -1, 0, -1, -1, 0,
      1, 0, 1, -1, 0, 1, 1, 0, -1, -1, 0, -1,
      0, 1, 1, 0, -1, 1, 0, 1, -1, 0, -1, -1
    ]);

    this.noise3D = function (xin, yin, zin) {
      var n0, n1, n2, n3;
      var s = (xin + yin + zin) * F3;
      var i = Math.floor(xin + s), j = Math.floor(yin + s), k = Math.floor(zin + s);
      var t = (i + j + k) * G3;
      var X0 = i - t, Y0 = j - t, Z0 = k - t;
      var x0 = xin - X0, y0 = yin - Y0, z0 = zin - Z0;
      var i1, j1, k1, i2, j2, k2;
      if (x0 >= y0) {
        if (y0 >= z0) { i1 = 1; j1 = 0; k1 = 0; i2 = 1; j2 = 1; k2 = 0; }
        else if (x0 >= z0) { i1 = 1; j1 = 0; k1 = 0; i2 = 1; j2 = 0; k2 = 1; }
        else { i1 = 0; j1 = 0; k1 = 1; i2 = 1; j2 = 0; k2 = 1; }
      } else {
        if (y0 < z0) { i1 = 0; j1 = 0; k1 = 1; i2 = 0; j2 = 1; k2 = 1; }
        else if (x0 < z0) { i1 = 0; j1 = 1; k1 = 0; i2 = 0; j2 = 1; k2 = 1; }
        else { i1 = 0; j1 = 1; k1 = 0; i2 = 1; j2 = 1; k2 = 0; }
      }
      var x1 = x0 - i1 + G3, y1 = y0 - j1 + G3, z1 = z0 - k1 + G3;
      var x2 = x0 - i2 + 2.0 * G3, y2 = y0 - j2 + 2.0 * G3, z2 = z0 - k2 + 2.0 * G3;
      var x3 = x0 - 1.0 + 3.0 * G3, y3 = y0 - 1.0 + 3.0 * G3, z3 = z0 - 1.0 + 3.0 * G3;
      var ii = i & 255, jj = j & 255, kk = k & 255;
      var gi0 = permMod12[ii + perm[jj + perm[kk]]] * 3;
      var gi1 = permMod12[ii + i1 + perm[jj + j1 + perm[kk + k1]]] * 3;
      var gi2 = permMod12[ii + i2 + perm[jj + j2 + perm[kk + k2]]] * 3;
      var gi3 = permMod12[ii + 1 + perm[jj + 1 + perm[kk + 1]]] * 3;
      var t0 = 0.6 - x0 * x0 - y0 * y0 - z0 * z0;
      if (t0 < 0) n0 = 0.0;
      else { t0 *= t0; n0 = t0 * t0 * (grad3[gi0] * x0 + grad3[gi0 + 1] * y0 + grad3[gi0 + 2] * z0); }
      var t1 = 0.6 - x1 * x1 - y1 * y1 - z1 * z1;
      if (t1 < 0) n1 = 0.0;
      else { t1 *= t1; n1 = t1 * t1 * (grad3[gi1] * x1 + grad3[gi1 + 1] * y1 + grad3[gi1 + 2] * z1); }
      var t2 = 0.6 - x2 * x2 - y2 * y2 - z2 * z2;
      if (t2 < 0) n2 = 0.0;
      else { t2 *= t2; n2 = t2 * t2 * (grad3[gi2] * x2 + grad3[gi2 + 1] * y2 + grad3[gi2 + 2] * z2); }
      var t3 = 0.6 - x3 * x3 - y3 * y3 - z3 * z3;
      if (t3 < 0) n3 = 0.0;
      else { t3 *= t3; n3 = t3 * t3 * (grad3[gi3] * x3 + grad3[gi3 + 1] * y3 + grad3[gi3 + 2] * z3); }
      return 32.0 * (n0 + n1 + n2 + n3);
    };
  }

  function startTubes() {
    if (typeof THREE === 'undefined') return;

    var noise = new FastSimplex();
    var width = container.clientWidth || window.innerWidth;
    var height = container.clientHeight || 600;

    var scene = new THREE.Scene();
    var camera = new THREE.PerspectiveCamera(65, width / height, 0.1, 1000);
    camera.position.z = 140;

    var renderer = new THREE.WebGLRenderer({
      canvas: canvas,
      alpha: true,
      antialias: true,
      powerPreference: 'high-performance'
    });
    renderer.setPixelRatio(Math.min(window.devicePixelRatio || 1, 1.5));
    renderer.setSize(width, height);

    // Ambient light
    var ambient = new THREE.AmbientLight(0x1a2e12, 1.2);
    scene.add(ambient);

    // Orbiting point lights with brand colors
    var lights = [
      { light: new THREE.PointLight(0x7db82a, 2.4, 250), speed: 0.0012, radius: 100, yOffset: 20 },
      { light: new THREE.PointLight(0xa3d44f, 2.0, 260), speed: -0.0015, radius: 120, yOffset: -30 },
      { light: new THREE.PointLight(0xe8b020, 2.2, 220), speed: 0.0018, radius: 90, yOffset: 40 },
      { light: new THREE.PointLight(0x00bcd4, 1.8, 240), speed: -0.0010, radius: 110, yOffset: -10 }
    ];

    lights.forEach(function (item) {
      scene.add(item.light);
    });

    // Create Tubes
    var numTubes = 12;
    var pointsPerTube = 16;
    var tubes = [];
    var group = new THREE.Group();
    scene.add(group);

    // Material with high metallic sheen to catch orbiting lights
    var tubeMaterial = new THREE.MeshStandardMaterial({
      color: 0x16240d,
      roughness: 0.35,
      metalness: 0.75,
      wireframe: false
    });

    for (var t = 0; t < numTubes; t++) {
      var initialPoints = [];
      var xOffset = (t - (numTubes - 1) / 2) * 22;
      for (var p = 0; p < pointsPerTube; p++) {
        var y = (p - (pointsPerTube - 1) / 2) * 16;
        initialPoints.push(new THREE.Vector3(xOffset, y, 0));
      }

      var curve = new THREE.CatmullRomCurve3(initialPoints);
      var geom = new THREE.TubeGeometry(curve, 48, 2.2, 8, false);
      var mesh = new THREE.Mesh(geom, tubeMaterial);
      group.add(mesh);

      tubes.push({
        mesh: mesh,
        curve: curve,
        baseX: xOffset,
        points: initialPoints,
        speed: 0.0006 + Math.random() * 0.0004,
        offset: Math.random() * 100
      });
    }

    // Mouse tilt tracking
    var mouseX = 0, mouseY = 0;
    var targetMouseX = 0, targetMouseY = 0;
    var outcomesEl = document.getElementById('outcomes');

    if (outcomesEl) {
      outcomesEl.addEventListener('mousemove', function (e) {
        var rect = outcomesEl.getBoundingClientRect();
        targetMouseX = ((e.clientX - rect.left) / rect.width - 0.5) * 2;
        targetMouseY = ((e.clientY - rect.top) / rect.height - 0.5) * 2;
      }, { passive: true });

      outcomesEl.addEventListener('mouseleave', function () {
        targetMouseX = 0;
        targetMouseY = 0;
      }, { passive: true });
    }

    // Resize handling
    function onResize() {
      if (!container) return;
      width = container.clientWidth;
      height = container.clientHeight;
      if (width === 0 || height === 0) return;
      camera.aspect = width / height;
      camera.updateProjectionMatrix();
      renderer.setSize(width, height);
    }
    window.addEventListener('resize', onResize, { passive: true });

    // Animation Loop with Visibility Control
    var animId = null;
    var isRunning = false;
    var time = 0;

    function renderFrame() {
      if (!isRunning) return;

      time += 0.008;

      // Smooth mouse follow
      mouseX += (targetMouseX - mouseX) * 0.05;
      mouseY += (targetMouseY - mouseY) * 0.05;

      group.rotation.y = mouseX * 0.15;
      group.rotation.x = -mouseY * 0.12;

      // Orbit lights
      lights.forEach(function (item, idx) {
        var a = time * 20 * item.speed + idx * (Math.PI / 2);
        item.light.position.x = Math.sin(a) * item.radius;
        item.light.position.z = Math.cos(a) * item.radius;
        item.light.position.y = Math.sin(a * 0.7) * 40 + item.yOffset;
      });

      // Update tube curves using simplex noise
      for (var i = 0; i < tubes.length; i++) {
        var tb = tubes[i];
        var pts = tb.points;
        for (var k = 0; k < pts.length; k++) {
          var py = (k - (pts.length - 1) / 2) * 16;
          var nx = noise.noise3D(tb.baseX * 0.02 + tb.offset, py * 0.02, time * 0.8) * 18;
          var nz = noise.noise3D(tb.baseX * 0.02, py * 0.02 + tb.offset, time * 0.7) * 25;
          pts[k].x = tb.baseX + nx;
          pts[k].y = py;
          pts[k].z = nz;
        }

        tb.curve.points = pts;
        tb.mesh.geometry.dispose();
        tb.mesh.geometry = new THREE.TubeGeometry(tb.curve, 48, 2.2, 8, false);
      }

      renderer.render(scene, camera);
      animId = requestAnimationFrame(renderFrame);
    }

    // IntersectionObserver: Only render while visible
    if ('IntersectionObserver' in window && outcomesEl) {
      var observer = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
          if (entry.isIntersecting) {
            if (!isRunning) {
              isRunning = true;
              onResize();
              animId = requestAnimationFrame(renderFrame);
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

      observer.observe(outcomesEl);
    } else {
      isRunning = true;
      animId = requestAnimationFrame(renderFrame);
    }
  }

  // Load Three.js dynamically if not already available, then start
  if (typeof THREE !== 'undefined') {
    startTubes();
  } else {
    var script = document.createElement('script');
    script.src = 'https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js';
    script.async = true;
    script.onload = startTubes;
    document.head.appendChild(script);
  }
})();
