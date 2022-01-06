class Game {
    constructor(arrivalId) {
        this.divId = "game";
        this.arrivalId = arrivalId;
        this.config = {
            type: Phaser.CANVAS,
            parent: "game",
            width: 1280,
            height: 720,
            scale: {
                // Fit to window
                //scaleMode : Phaser.ScaleManager.SHOW_ALL,
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
        this.load.image('1_car', '/js/assets/1_car.png');
        this.load.image('2_car', '/js/assets/2_car.png');
        this.load.image('3_car', '/js/assets/3_car.png');
        this.load.image('4_car', '/js/assets/4_car.png');
        this.load.image('background', '/js/assets/background.jpg');
        this.load.image('player_killer', '/js/assets/player_killer.png');
        this.load.image('ball', '/js/assets/ball.png')
    }

    async getRacerByUserId() {
        const answer = await fetch(`api/?method=getRacerByUserId`);
        return await answer.json();
    }

    async getToken() {
        const answer = await fetch(`api/?method=checkCookie`);
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

    async getPlayerKillerByArrivalId() {
        const answer = await fetch(`api/?method=getPlayerKillerByArrivalId&arrival_id=${this.game.arrivalId}`);
        return await answer.json();
    }

    async getСoordinates(racers) {
        const answer = await fetch(`api/?method=getСoordinates&racerId=${racers}`);
        return await answer.json();
    }

    async getAllCoordinates(racer1,racer2,racer3,racer4) {
        const answer = await fetch(`api/?method=getAllCoordinates&racer1=${racer1}&racer2=${racer2}&racer3=${racer3}&racer4=${racer4}&arrival_id=${this.game.arrivalId}&w_width=${this.config.width}&w_height=${this.config.height}`);
        return await answer.json();
    }

    async raceCommand(command) {
        const answer = await fetch(`api/?method=raceCommand&command=${command}&w_width=${this.config.width}&w_height=${this.config.height}`);
        return await answer.json();
    }

    async timer() {
        const answer = await fetch(`api/?method=timer&arrival_id=${this.game.arrivalId}&w_width=${this.config.width}`);
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
            this.ball = this.add.sprite(x, y, 'ball');
            this.ball.displayHeight = 60
            this.ball.displayWidth = 60;
            //this.idk = this.add.circle(x, y, 10, 0xffffff);
            this.ball.id = ballCoordinates['data'].id;
        }
    }

    async createPlayerKiller() {
        if (this.playerKiller) {
            this.playerKiller.destroy();
        }
        const playerKillerCoordinates = await this.getBallByArrivalId();
        if (playerKillerCoordinates) {
            var x = playerKillerCoordinates['data'].x;
            var y = playerKillerCoordinates['data'].y;
            //this.idk = this.add.circle(x, y, 50, 0xb00000);
            this.playerKiller = this.add.sprite(x, y, 'player_killer');
            this.playerKiller.displayHeight = 70;
            this.playerKiller.displayWidth = 70;
            this.playerKiller.id = playerKillerCoordinates['data'].id;
        }
    }

    async leaveArrival(token) {
        const answer = await fetch(
            `api/?method=leaveArrival&token=${token}&id=${this.game.arrivalId}`
        );
        return await answer.json();
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
                        this.player.displayWidth = 55;
                        this.player.displayHeight = 90;
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
                const infoDiv = document.createElement('div');
                infoDiv.id = 'infodiv';
                document.getElementById('game').appendChild(infoDiv);

                this.spamLocation();
            }
            

    }

    create() {
        //about background
        const background = this.add.image(0, 0, `background`).setOrigin(0,0);
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

        this.createPlayerKiller();
        const divInfo = document.createElement('div');
        document.getElementById('game').appendChild(divInfo);
        divInfo.id = 'divInfo';
        if(divInfo) {
            document.getElementById(divInfo.id).innerHTML = `<p id = "count"></p>
            <button id = "exit" class = "btn">Выйти</button>`;
        }
        document.getElementById('exit').addEventListener('click', async () => {
            const token = await this.getToken();
            if (token) {
                this.leaveArrival(token['data'].token);
                const mapList = new MapList();
                const form = new Form();
                form.insertTemplate(mapList.divId, token['data']);
            }
        });
        
        
    }


    spamLocation() {
        //const infoDiv = document.getElementById('infoDiv');
                    
        this.info = [-1,-1,-1,-1]
        var interval = setInterval(() => {
            this.getAllCoordinates(
                this.playerGroup.children.entries[0].id,
                this.playerGroup.children.entries[1].id,
                this.playerGroup.children.entries[2].id,
                this.playerGroup.children.entries[3].id).then(coordinates => {
                //console.log(coordinates['data']);

                //console.log(coordinates['data'][0].coin, coordinates['data'][1].coin, coordinates['data'][2].coin, coordinates['data'][3].coin);
                if(coordinates['data'][6] != true) {
                    for (let i = 0; i < this.playerGroup.children.entries.length; i++) {
                        if ( coordinates['data'][i].life = 1) { //типа не нагружать клиент ???????
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
                        if(this.info[i] != coordinates['data'][i].coin) {
                            this.info[i] = coordinates['data'][i].coin;
                            document.getElementById('count').innerHTML = `
                            <p class = "textResult">ID ${coordinates['data'][0].id} : ${coordinates['data'][0].coin}</p></br>
                            <p class = "textResult">ID ${coordinates['data'][1].id} : ${coordinates['data'][1].coin}</p></br>
                            <p class = "textResult">ID ${coordinates['data'][2].id} : ${coordinates['data'][2].coin}</p></br>
                            <p class = "textResult">ID ${coordinates['data'][3].id} : ${coordinates['data'][3].coin}</p>
                            `;
                        }
                    }

                    this.ball.x = coordinates['data'][4].x;
                    this.ball.y = coordinates['data'][4].y;
                    this.ball.angle++;
    
                    this.playerKiller.x = coordinates['data'][5].x;
                    this.playerKiller.y = coordinates['data'][5].y;
                    this.playerKiller.angle++;
                    //console.log(coordinates);
                }
                if(coordinates['data'][6] == true) {
                    clearInterval(interval);
                    document.getElementById('game').innerHTML = `
                    <div id = "divEnd" class = "internalDiv">
                        <p id = "textInfo"> Игра окончена </p>
                        <p class = "textResult">ID ${coordinates['data'][0].id} : ${coordinates['data'][0].coin} оч.</p></br>
                        <p class = "textResult">ID ${coordinates['data'][1].id} : ${coordinates['data'][1].coin} оч.</p></br>
                        <p class = "textResult">ID ${coordinates['data'][2].id} : ${coordinates['data'][2].coin} оч.</p></br>
                        <p class = "textResult">ID ${coordinates['data'][3].id} : ${coordinates['data'][3].coin} оч.</p>
                        <button id = "exitEnd" class = "btn">Выйти</button>
                    </div>
                    `;
                    const exitEnd = document.getElementById('exitEnd');
                    if (exitEnd) {
                        exitEnd.addEventListener('click', async () => {
                            const token = await this.getToken();
                            console.log('dfsf')
                            if (token) {
                                const mapList = new MapList();
                                const form = new Form();
                                form.insertTemplate(mapList.divId, token['data']);
                            }
                        });    
                    }
                }
                
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