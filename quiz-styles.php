<style>
  .info-soal {
    text-align: left;
    font-size: 14px;
    padding-left: 15px;
    border-top: solid 1px #ccc;
    border-bottom: solid 1px #ccc;
    padding: 10px 0;
    margin: 15px 0;
    display: grid;
    grid-template-columns: 50% 50%;
  }

  .kalimat-soal {
    font-size: 18px;
    color: yellow;
    font-weight: 600;
  }

  .blok-opsi {
    display: grid;
    grid-template-columns: 50% 50%;
    gap: 5px;
    margin-top: 10px;
    margin-bottom: 30px;
  }

  .opsi {
    background: linear-gradient(#005, #00a);
    padding: 10px;
    border-radius: 5px;
    cursor: pointer;
    transition: .3s;
    color: #aa5;
  }

  .opsi-selected {
    background: linear-gradient(#aa0, #330);
    border: solid 2px yellow;
    color: white;
    font-weight: 600;
  }

  .opsi:hover {
    background: linear-gradient(#aa0, #330);
    letter-spacing: .5px;
    color: white;
  }

  .blok-timer {
    display: flex;
    justify-content: center;
    font-size: 50px;
    font-family: 'consolas', 'Courier New', Courier, monospace;
    margin-bottom: 20px;
  }

  .image-soal {
    max-width: 100%;
    max-height: 400px;
    display: block;
    margin: 10px auto;
  }
</style>