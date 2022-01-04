class Game {
    constructor(arrivalId) {
        this.divId = "game";
        this.arrivalId = arrivalId;
        this.config = {
            type: Phaser.CANVAS,
            parent: "game",
            width: window.innerWidth,
            height: window.innerHeight,
            scale: {
                // Fit to window
                mode: Phaser.Scale.FIT,
                // Center vertically and horizontally
                autoCenter: Phaser.Scale.CENTER_BOTH,
            },
            physics: {
                default: "arcade",
                arcade: {
                    debug: true,
                    gravity: { y: 0 },
                },
            },

            scene: Game,
        };
    }

    preload() {
        this.load.image("1_car", "assets/1_car.png");
        this.load.image("2_car", "assets/2_car.png");
        this.load.image("3_car", "assets/3_car.png");
        this.load.image("4_car", "assets/4_car.png");
        this.load.image("background", "assets/background.jpg");
    }

    async getRacerByUserId() {
        const answer = await fetch(`api/?method=getRacerByUserId`);
        return await answer.json();
    }

    async getRacers() {
        const arrivalId = this.game.arrivalId;
        var coordinates;
        async function getRacers() {
            const answer = await fetch(
                `api/?method=getRacers&arrivalId=${arrivalId}`
            );
            return await answer.json();
        }
        const racers = await getRacers();
        if (racers) {
            coordinates = await this.getLocation(racers["data"]);
            if (coordinates) return coordinates;
        }
    }

    async getBallByArrivalId(){
        const answer = await fetch(`api/?method=getBallByArrivalId&arrival_id=${this.game.arrivalId}`);
        return await answer.json();
    }

    async getСoordinates(racers) {
        const answer = await fetch(`api/?method=getСoordinates&racerId=${racers}`);
        return await answer.json();
    }

    async getAllCoordinates(racer1,racer2,racer3,racer4) {
        const answer = await fetch(`api/?method=getAllCoordinates&racer1=${racer1}&racer2=${racer2}&racer3=${racer3}&racer4=${racer4}&arrival_id=${this.game.arrivalId}&w_width=${window.innerWidth}&w_height=${window.innerHeight}`);
        return await answer.json();
    }

    async raceCommand(command) {
        const answer = await fetch(`api/?method=raceCommand&command=${command}&w_width=${window.innerWidth}&w_height=${window.innerHeight}`);
        return await answer.json();
    }

    async timer() {
        const answer = await fetch(`api/?method=timer&arrival_id=${this.game.arrivalId}&w_width=${window.innerWidth}`);
        return await answer.json();
    }

    async getLocation(racers) {
        let coordinates = [];
        var info;
        for (let i = 0; i < racers.length; i++) {
            info = await this.getСoordinates(racers[i]);
            if (info) {
                coordinates.push(info["data"]);
            }
        }
        return coordinates;
    }

    async createBall() {
        if (this.ball) {
            this.ball.destroy();
        }
        const ballCoordinates = await this.getBallByArrivalId();
        if (ballCoordinates) {
            var x = ballCoordinates['data'].x;
            var y = ballCoordinates['data'].y;
            this.ball = this.add.circle(x, y, 10, 0xffffff);
            this.ball.id = ballCoordinates['data'].id;
        }
    }



    async createCars() {
        if (this.player) {
            this.player.destroy();
        }
        var x;
        var y;
        this.playerGroup = this.add.group();
        this.playerNameGroup = this.add.group();
        const coordinates = await this.getRacers(); 
        if (coordinates) {
            const racerId = await this.getRacerByUserId();
            if (racerId) {
                let index = 1;
                for (let i = 0; i < coordinates.length; i++) {
                    x = coordinates[i].x;
                    y = coordinates[i].y;
                    if (x && y) {
                        this.player = this.add.image(x, y, `${index}_car`); // 15,  0xffffff)//);
                        this.player.displayWidth = 50;
                        this.player.displayHeight = 80;
                        if (this.player) {
                            this.player.id = coordinates[i].id;
                            if (racerId['data'].id == this.player.id) {
                                this.playerName = this.add.text(x, y, 'you');
                                this.playerName.id = coordinates[i].id;
                            } else {
                                this.playerName = this.add.text(x, y, `${coordinates[i].id}`);
                                this.playerName.id = coordinates[i].id;
                            }
                        }
                    }
                    this.playerGroup.add(this.player);
                    this.playerNameGroup.add(this.playerName);
                    index++;
                }
            }
        }
        if (this.playerGroup.children.entries[0].id &&
            this.playerGroup.children.entries[1].id &&
            this.playerGroup.children.entries[2].id &&
            this.playerGroup.children.entries[3].id) {
                this.spamLocation();
            }

    }

    create() {
        //about background
        const background = this.add.rectangle(0,0, window.innerWidth, window.innerHeight, 0x000000).setOrigin(0, 0);
        background.displayWidth = this.config.width;
        background.displayHeight = this.config.height;

        //about ball
        this.createBall();

        //about car
        this.createCars();
        //about keyboard
        this.cursorUp = this.input.keyboard.addKey('W');
        this.cursorLeft = this.input.keyboard.addKey('A');
        this.cursorDown = this.input.keyboard.addKey('S');
        this.cursorRight = this.input.keyboard.addKey('D');
        //this.carWIDTH = this.car.displayWidth;

        this.laser = this.add.rectangle(-1, window.innerHeight/2, 6, window.innerHeight, 0xffffff);
        this.laserKill = this.add.rectangle(-1, window.innerHeight/2, 6, window.innerHeight, 0x9b111e);
        //setInterval(async () => {
        //    await this.timer();
        //}, 5);
        this.timer();
    }

    carSystem() {
        //const carWIDTH = this.car.displayWidth;
        //const carHEIGHT = this.car.displayHeight;
        //const carParts = carWIDTH/3;
    }

    spamLocation() {
        
        setInterval(() => {
            this.getAllCoordinates(
                this.playerGroup.children.entries[0].id,
                this.playerGroup.children.entries[1].id,
                this.playerGroup.children.entries[2].id,
                this.playerGroup.children.entries[3].id).then(coordinates => {
                //console.log(coordinates['data']);
                for (let i = 0; i < this.playerGroup.children.entries.length; i++) {
                    if ( coordinates['data'][i].life = 1) {
                        if (coordinates['data'][i].y && coordinates['data'][i].x) {
                            if ((this.playerGroup.children.entries[i].x != coordinates['data'][i].x)) {
                                this.playerGroup.children.entries[i].x = coordinates['data'][i].x;
                                this.playerNameGroup.children.entries[i].x = coordinates['data'][i].x;
                            }
                            if ((this.playerGroup.children.entries[i].y != coordinates['data'][i].y)) {
                                this.playerGroup.children.entries[i].y = coordinates['data'][i].y;
                                this.playerNameGroup.children.entries[i].y = coordinates['data'][i].y;
                            }
                            this.playerGroup.children.entries[i].angle = coordinates['data'][i].angle;
                        }
                    }
                    
                }
                this.ball.x = coordinates['data'][4].x;
                this.ball.y = coordinates['data'][4].y;
                if (this.laser.x != coordinates['data'][5].x) {
                    this.laser.x = coordinates['data'][5].x;
                    //this.shadow.x = 50;
                }
                if (this.laserKill.x != coordinates['data'][5].x2) {
                    this.laserKill.x = coordinates['data'][5].x2;
                    //this.shadow.x = 50;
                }
                
                //console.log(coordinates['data'][5]);
            });
        }, 1000/60);
        
    }
    
    update() {
        if (this.cursorUp.isDown) {
            this.raceCommand('W');
        }
        if (this.cursorLeft.isDown) {
            this.raceCommand('A');
        }
        if (this.cursorDown.isDown) {
            this.raceCommand('S');
        }
        if (this.cursorRight.isDown) {
            this.raceCommand('D');
        }
    }

    async render() {
        console.log(this.arrivalId);
        this.game = new Phaser.Game(this.config);
        this.game.arrivalId = this.arrivalId;
        //this.preload(); ////?????????????
        image.onload = async function() {
            //this.create(); //?????????????
            //this.update(); //?????????????
        }
    }
}