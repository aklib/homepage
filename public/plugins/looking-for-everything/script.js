var s = c.width = c.height = window.innerWidth / 4,
        ctx = c.getContext('2d'),
        opts = {
            particles: 200,
            particleBaseSize: 4,
            particleAddedSize: 1,
            particleMaxSize: 5,
            particleBaseLight: 5,
            particleAddedLight: 30,
            particleBaseBaseAngSpeed: .001,
            particleAddedBaseAngSpeed: .001,
            particleBaseVariedAngSpeed: .0005,
            particleAddedVariedAngSpeed: .0005,
            sourceBaseSize: 3,
            sourceAddedSize: 3,
            sourceBaseAngSpeed: -.01,
            sourceVariedAngSpeed: .005,
            sourceBaseDist: 130,
            sourceVariedDist: 50,

            particleTemplateColor: 'hsla(hue,80%,light%,alp)',
            repaintColor: 'rgba(0,0,0,.5)',
            enableTrails: false},
        util = {
            square: x => x * x,
            tau: 6.28},
        particles = [],
        source = new Source(),
        tick = 0;

function Particle() {
    this.dist = Math.random() * s / 2;
    this.rad = Math.random() * util.tau;

    this.baseAngSpeed = opts.particleBaseBaseAngSpeed + opts.particleAddedBaseAngSpeed * Math.random();
    this.variedAngSpeed = opts.particleBaseVariedAngSpeed + opts.particleAddedVariedAngSpeed * Math.random();
    this.size = opts.particleBaseSize + opts.particleAddedSize * Math.random();
}
Particle.prototype.step = function () {
    var angSpeed = this.baseAngSpeed + this.variedAngSpeed * Math.sin(this.rad * 7 + tick / 100);
    this.rad += angSpeed;

    var x = this.dist * Math.cos(this.rad),
            y = this.dist * Math.sin(this.rad),
            squareDist = util.square(x - source.x) + util.square(y - source.y),
            sizeProp = Math.pow(s, 1 / 2) / Math.pow(squareDist, 1 / 2),
            color = opts.particleTemplateColor.
            replace('hue', this.rad / util.tau * 360 + tick).
            replace('light', opts.particleBaseLight + sizeProp * opts.particleAddedLight).
            replace('alp', .8);
//console.log(tick);
    ctx.fillStyle = color;
    ctx.beginPath();
    ctx.arc(x, y, Math.min(this.size * sizeProp, opts.particleMaxSize), 0, util.tau);
    ctx.fill();
};

function Source() {
    this.x = 0;
    this.y = 0;
    this.rad = Math.random() * util.tau;
}
Source.prototype.step = function () {

    if (!this.mouseControlled) {
        var angSpeed = opts.sourceBaseAngSpeed + Math.sin(this.rad * 6 + tick / 100) * opts.sourceVariedAngSpeed;
        this.rad += angSpeed;

        var dist = opts.sourceBaseDist + Math.sin(this.rad * 5 + tick / 100) * opts.sourceVariedDist;

        this.x = dist * Math.cos(this.rad);
        this.y = dist * Math.sin(this.rad);
    }

    ctx.fillStyle = 'white';
    ctx.beginPath();
    ctx.arc(this.x, this.y, 1, 0, util.tau);
    ctx.fill();
};

function anim() {

    window.requestAnimationFrame(anim);

    ++tick;

    if (!opts.enableTrails)
        ctx.globalCompositeOperation = 'source-over';

    ctx.fillStyle = opts.repaintColor;
    ctx.fillRect(0, 0, s, s);

    ctx.globalCompositeOperation = 'lighter';

    if (particles.length < opts.particles)
        particles.push(new Particle());

    ctx.translate(s / 2, s / 2);

    source.step();

    particles.map(particle => particle.step());
    ctx.translate(-s / 2, -s / 2);
}

ctx.fillStyle = '#222';
ctx.fillRect(0, 0, s, s);
anim();

c.addEventListener('mousemove', e => {

    var bbox = c.getBoundingClientRect();

    source.x = e.clientX - bbox.left - s / 2;
    source.y = e.clientY - bbox.top - s / 2;
    source.mouseControlled = true;
});
c.addEventListener('mouseleave', e => {

    var bbox = c.getBoundingClientRect();

    source.x = e.clientX - bbox.left - s / 2;
    source.y = e.clientY - bbox.top - s / 2;

    source.rad = Math.atan(source.y / source.x);
    if (source.x < 0)
        source.rad += Math.PI;

    source.mouseControlled = false;
});

window.addEventListener('resize', function () {
    s = c.width = c.height = window.innerWidth / 4;
    ctx = c.getContext('2d');
    ctx.fillStyle = '#222';
    ctx.fillRect(0, 0, s, s);
    anim();
});

