window.onload = async function() {
    var data = ['9'];
    const game = new Game(data);
    await game.render();
};