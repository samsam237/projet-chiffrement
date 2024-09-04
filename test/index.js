function hexToUint8Array(hex) {
    if (hex.length % 2 !== 0) {
        throw new Error('Invalid hex string');
    }
    
    var byteArray = new Uint8Array(hex.length / 2);
    for (var i = 0; i < byteArray.length; i++) {
        byteArray[i] = parseInt(hex.substr(i * 2, 2), 16);
    }
    return byteArray;
}

function parseHexToText(hex){
    var decoder = new TextDecoder(); 
    return decoder.decode(hexToUint8Array(hex));
}

async function encryptMessage(message) {
    try {
        const response = await fetch('http://localhost:8000/encrypt', {
            method: 'POST',
            headers: {
                'content-type': 'application/json'
            },
            body: JSON.stringify({ "message": message })
        });

        const data = await response.json();

        console.log('Encrypted message:', parseHexToText(data.message) );
        return data.message;
    } catch (error) {
        console.error('Error during ecryption fetch:', error.message);
    }
}

async function decryptMessage(encryptedMessage) {
    try {
        const response = await fetch('http://localhost:8000/decrypt', {
            method: 'POST',
            headers: {
                'content-type': 'application/json'
            },
            body: JSON.stringify({ "message": encryptedMessage })
        });

        if (!response.ok) {
            throw new Error('Network response was not ok');
        }

        const data = await response.json();
        console.log('Decrypted message:', parseHexToText(data.message) );
        return data.message;
    } catch (error) {
        console.error('Error during decryption fetch:', error);
    }
}

encryptMessage('Hello World!')
    .then((data) => {
        if (!data) {return}
        console.log(`data to decrypt ${data}`);
        decryptMessage(data)
            .then((res) => {
                console.log('fin.');
                document.body.innerHTML = res;
            }).catch((error) => {
                console.error(error);
            });
    }).catch((error) => {
        console.error(error);
    });

