class Game {
    constructor(arrivalId) {
        this.divId = 'game';
        this.arrivalId = arrivalId;
        this.config = {
            type: Phaser.CANVAS,
            parent: 'game',
            width : window.innerWidth,
            height : window.innerHeight,
            scale: {
                // Fit to window
                mode: Phaser.Scale.FIT,
                // Center vertically and horizontally
                autoCenter: Phaser.Scale.CENTER_BOTH
            },
            physics: {
                default: 'arcade',
                arcade: {
                    debug: true,
                    gravity: { y: 0 }
                }
            },
            
            scene: Game
        };
    }

    preload() {
        this.load.image('1_car', "/js/assets/1_car.png");
        this.load.image('2_car', "/js/assets/2_car.png");
        this.load.image('3_car', "/js/assets/3_car.png");
        this.load.image('4_car', "/js/assets/4_car.png");
        this.load.image('background', "/js/assets/background.jpg");
    }

    async requests () {
        const arrivalId = this.game.arrivalId;
        //остается определить пользователя (его id) (думаю лучше сверить в бэке) и реализовать отрисовку по начальным координатам 
        //позже приступить к командам по изменению координат и отрисовки в новом положении через setInterval 
        async function getRacers() {
            const answer = await fetch(
                `api/?method=getRacers&arrivalId=${arrivalId}`
            );
            return await answer.json();
        }
        
        const racers = await getRacers();
        if(racers) {
            const racer1 = racers['data'][0];
            const racer2 = racers['data'][1];
            const racer3 = racers['data'][2];
            const racer4 = racers['data'][3];
        }
    }

    create() {

        //about background
        const background = this.add.image(0,0, 'background').setOrigin(0,0);
        background.displayWidth = this.config.width;
        background.displayHeight = this.config.height;

        //about coin
        this.coin = this.add.circle(70, 0, 10, 0xffffff);
        this.physics.add.existing(this.coin);
        this.coin.body.setVelocity(0,200);
        this.coin.body.setCollideWorldBounds(true, 2, 1);
        this.coin.body.setBounce(1,1);
        
        //about car
        this.car = this.add.image(100,100, '4_car');
        this.car.displayWidth = 50;
        this.car.displayHeight = 100;
        this.physics.add.existing(this.car);
        this.car.body.setBounce(0,0);
        this.add.text(2, 2, 'ну пока так');

        //about keyboard
        this.cursorUp = this.input.keyboard.addKey('W');
        this.cursorLeft = this.input.keyboard.addKey('A');
        this.cursorDown = this.input.keyboard.addKey('S');
        this.cursorRight = this.input.keyboard.addKey('D');
        //this.carWIDTH = this.car.displayWidth;
    }

    carSystem() {
       //const carWIDTH = this.car.displayWidth;
       //const carHEIGHT = this.car.displayHeight;

       //const carParts = carWIDTH/3;
    }

    update() { 
        //about moving
        //->9
        this.carSystem()
        if(this.cursorUp.isDown) {
            this.car.y -= 5;
            this.car.angle -=0;
            this.requests();
            //console.dir(this.game); //->undefined
            //this.requests();
            
        }
        if (this.cursorLeft.isDown) {
            this.car.x -= 5;
            this.car.angle -= 0;
        }
        if (this.cursorDown.isDown) {
            this.car.y += 5;
            this.car.angle += 0;
        }
        if (this.cursorRight.isDown) {
            this.car.x += 5;
            this.car.angle += 0;

        }
    }

    render() {
        //const arrival = this.arrivalId;
        console.log(this.arrivalId); //->9
            //this.arrival = this.arrivalId
            this.game = new Phaser.Game(this.config);
            this.game.arrivalId = this.arrivalId;
            this.preload();
            this.create();
            this.update();
    }

}