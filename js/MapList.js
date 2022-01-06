class MapList {
    constructor(data) {
        this.divId = 'mapList';
        this.data = data;
    }

    logout() {
        let data = this.data;
        const form = new Form();

        if (data) {
            async function logout(token) {
                await fetch(
                    `api/?method=logout&token=${token}`
                );
            }
            const logOutBtn = document.getElementById('logOutBtn');

            logOutBtn.addEventListener('click', async function () {
                if (data) {
                    await logout(data['token']);
                }
                data = null;
                const auth = new Auth();
                form.insertTemplate(auth.divId);
            });
        }
    }

    arrivalFuncs() {
        const token = this.data['token'];

        async function getStatus(roomId) {
            const answer = await fetch(
                `api/?method=checkStatus&arrivalId=${roomId}`
            );
            return await answer.json();
        }

        async function joinRoom(token, roomId) {
            const answer = await fetch(
                `api/?method=joinArrival&token=${token}&arrivalId=${roomId}`
            );
            return await answer.json();
        }

        //сделаем эту функцию в другом месте
        async function leaveArrival(token, roomId) {
            const answer = await fetch(
                `api/?method=leaveArrival&token=${token}&id=${roomId}`
            );
            return await answer.json();
        }

        async function analiseStatus(roomId) {
            let plan;
            let status = await getStatus(roomId);
            if (status['data'].status == 'racing') {
                plan = false;
                clearInterval(timer);
            } else {
                plan = true;
                console.log(status['data'].status); 
            }
            return plan;
        }
        var timer;
        function getPlanByStatus (plan, roomId) {
            const form = new Form();
            timer = setInterval(async function() {
                if (plan == true) {
                    plan = await analiseStatus(roomId);
                    if (plan == false) {
                        document.getElementById(`${roomId}`).classList.add('entered');
                        const game = new Game();
                        form.insertTemplate(game.divId, [`${roomId}`]);
                    }
                }
                return plan;
            }
            , 500);
            return plan;
        } 

        //тут будет написан запрос в бэк на подключение к комнате и выход из нее
        document.addEventListener('click', async function (e) { //тут мы получаем id комнаты для подключения к ней
             if (e.target.classList.contains('roomDiv')) {
                const form = new Form();
                const roomId = e.target.id;
                let elements = document.getElementsByClassName('entered');
                if (elements.length == 1) {
                    let removedRoom = elements[0].id;
                    clearInterval(timer);
                    console.log(elements[0].id);
                    await leaveArrival(token, removedRoom); //вызываем метод покидания комнаты (я его отключал для добавления нескольких челиков в таблицу (проверить валидность метода joinArrival) )
                    document.getElementById(`${e.target.id}`).classList.remove('entered');
                    if (elements[0]) {
                        if (e.target.id != elements[0].id) {
                            removedRoom = elements[0].id;
                            console.log(3)
                            await leaveArrival(token, removedRoom);
                            document.getElementById(`${elements[0].id}`).classList.remove('entered');
                            let join = await joinRoom(token, roomId);
                            if (join) {
                                console.log(join['data']);
                                if (join['data'].status == 'open') {
                                    //const start = await roomsListUpdate(token);
                                    //roomsListUpdate(token);
                                    document.getElementById(`${e.target.id}`).classList.add('entered');
                                    let plan = await analiseStatus(roomId);
                                    if (plan == true) {
                                        getPlanByStatus(plan,roomId);
                                    }
                                    if (plan == false) {
                                        document.getElementById(`${e.target.id}`).classList.add('entered');
                                        const game = new Game();
                                        form.insertTemplate(game.divId, [`${e.target.id}`])
                                    }
                                }
                            }
                        }
                    }
                } else if (elements.length < 1) {
                    console.log(roomId);
                    let join = await joinRoom(token, roomId);
                    if (join) {
                        console.log(join['data']);
                        if (join['data'].status == 'open') {
                            //roomsListUpdate(token);
                            document.getElementById(`${e.target.id}`).classList.add('entered');
                            let plan = await analiseStatus(roomId);
                            if (plan == true) {
                                getPlanByStatus(plan,roomId);
                            }
                            if (plan == false) {
                                document.getElementById(`${e.target.id}`).classList.add('entered');
                                const game = new Game();
                                form.insertTemplate(game.divId, [`${e.target.id}`])
                            }
                                
                        }

                    }

                }
            }
            
        });
    }

    rooms() {
        const rooms = document.getElementById('rooms');
        const checkRoomsBtn = document.getElementById('checkRoomsBtn');
        const createRoomBtn = document.getElementById('createRoomBtn');
        roomsListUpdate(this.data['token']);

        async function getAllRooms(token) {
            const answer = await fetch(
                `api/?method=getAllRooms&token=${token}`
            );
            return await answer.json();
        }

        async function createRoom(token, roomName) { //тут отправляется запрос в бэк с названием комнаты от пользака
            const answer = await fetch(
                `api/?method=createRoom&token=${token}&name=${roomName}&raceId=1`
            );
            return await answer.json();
        }
        //setInterval(roomsListUpdate, 5000);

        async function roomsListUpdate(token) { //эта штука обновляет список 
            rooms.innerHTML = null;
            let answer = await getAllRooms(token); //получаем список комнат
            if (answer['data']) {
                if (answer['data'] != 'error') {
                    const roomList = answer['data'];
                    for (let i = 0; i < roomList.length; i++) {
                        let div = document.createElement('div');
                        div.id = `${roomList[i].id}`;;
                        div.classList.add('roomDiv');
                        document.getElementById('rooms').appendChild(div);
                        div.innerHTML = `[${roomList[i].status}] ${roomList[i].name}`; //тут должно начинаться заполнение блока комнаты (имя, статус, кол. игроков, название трассы)
                    }
                }
            }
        }

        checkRoomsBtn.addEventListener('click', async () => { //обновление комнат на кнопку
            roomsListUpdate(this.data['token']);
        });

        createRoomBtn.addEventListener('click', async () => { //кнопка создания комнаты
            let roomName = document.getElementById('roomNameInp').value;
            let newRoom = await createRoom(this.data['token'], roomName); //запилить автообновление после создания комнаты
            if (newRoom) {
                roomsListUpdate(this.data['token']);
            }
        });
    }

    render() {
        const mapListDiv = document.getElementById(this.divId);
        if (mapListDiv) {
            this.rooms();
            this.logout()
            this.arrivalFuncs();
        }
    }
}