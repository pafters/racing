class Game {
    constructor() {
        this.divId = 'game';
    }
    preload() {
        this.load.image('tiles', "js/assets/spritesheet_tiles2.png");
        this.load.tilemapTiledJSON('map', "js/assets/track2.json");
    }

    create() {
        const map = this.make.tilemap({ key: 'map', tileWidth: 128, tileHeight: 128 });
        const tileset = map.addTilesetImage('spritesheet_tiles2', 'tiles');

        this.grass = map.createStaticLayer(0, tileset, 0, 0);
        this.track = map.createStaticLayer(1, tileset, 0, 0);
        this.add.text(2, 2, 'ну пока так');

    }

    update() { }

    render() {
        var config = {
            type: Phaser.AUTO,
            parent: 'phaser-example',
            physics: {
                default: 'arcade',
                arcade: {
                    debug: false,
                    gravity: { y: 0 }
                }
            },
            scene: Game
        };

        var game = new Phaser.Game(config);

        const canvas = document.querySelector('canvas');
        document.getElementById(this.divId).appendChild(canvas);
        canvas.classList.add('gameDiv');

        this.preload();
        this.create();
        this.update();
    }

}