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

    joinArrival() {
        const token = this.data['token'];

        async function join(token, roomId) {
            await fetch(
                `api/?method=joinArrival&token=${token}&id=${roomId}`
            );
        }

        //тут будет написан запрос в бэк на подключение к комнате и выход из нее
        document.addEventListener('click', async function (e) { //тут мы получаем id комнаты для "подключения" к ней

            if (!e.target.classList.contains('roomDiv')) {
                return
            } else {

                const elements = document.getElementsByClassName('entered');
                //console.log(token);
                const roomId = e.target.value;
                console.log(roomId);
                await join(token, roomId);

                if (elements.length == 1) {
                    document.getElementById(`${e.target.id}`).classList.remove('entered');
                    if (elements[0]) {
                        if (e.target.id != elements[0].id) {
                            console.log(3)
                            document.getElementById(`${elements[0].id}`).classList.remove('entered');
                            document.getElementById(`${e.target.id}`).classList.add('entered');
                        }
                    }

                } else if (elements.length < 1) {
                    document.getElementById(`${e.target.id}`).classList.add('entered');
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
                `api/?method=createRoom&token=${token}&name=${roomName}`
            );
            return await answer.json();
        }
        //setInterval(roomsListUpdate, 5000);

        async function roomsListUpdate(token) { //эта штука обновляет список 
            rooms.innerHTML = null;
            let answer = await getAllRooms(token); //получаем список комнат
            if (answer.data) {
                const roomList = answer.data;
                for (let i = 0; i < roomList.length; i++) {
                    let div = document.createElement('div');
                    div.id = `room${i}`;
                    div.classList.add('roomDiv');
                    document.getElementById('rooms').appendChild(div);
                    div.value = roomList[i].id;
                    div.innerHTML = roomList[i].name; //тут должно начинаться заполнение блока комнаты (имя, статус, кол. игроков, название трассы)
                }
            }
        }

        checkRoomsBtn.addEventListener('click', async () => { //обновление комнат на кнопку
            roomsListUpdate(this.data['token']);
        });

        createRoomBtn.addEventListener('click', async () => { //кнопка создания комнаты
            let roomName = document.getElementById('roomNameInp').value;
            await createRoom(this.data['token'], roomName);
        });
    }

    render() {
        const mapListDiv = document.getElementById(this.divId);
        if (mapListDiv) {
            this.rooms();
            this.logout()
            this.joinArrival();
        }
    }
}