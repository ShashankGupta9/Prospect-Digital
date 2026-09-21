document.addEventListener('DOMContentLoaded', function() {
  const target = document.getElementById('vanta-birds-bg') || document.getElementById('hero');
  if (target && typeof VANTA !== 'undefined' && VANTA.BIRDS) {
    try {
      window.vantaEffect = VANTA.BIRDS({
        el: target,
        mouseControls: true,
        touchControls: true,
        gyroControls: false,
        minHeight: 200.00,
        minWidth: 200.00,
        scale: 1.00,
        scaleMobile: 1.00,
        backgroundColor: 0xffffff,
        color1: 0xca7777,
        color2: 0xb1d5d9,
        colorMode: "varianceGradient",
        quantity: 5.00,
        birdSize: 1.00,
        wingSpan: 30.00,
        speedLimit: 5.00,
        separation: 20.00,
        alignment: 20.00,
        cohesion: 20.00
      });
    } catch (e) {
      console.warn('Vanta Birds init:', e);
    }
  }
});
