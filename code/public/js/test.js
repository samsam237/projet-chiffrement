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
        console.log (data)
        console.log('Decrypted message:', parseHexToText(data.message) );
        return data.message;
    } catch (error) {
        console.error('Error during decryption fetch:', error);
    }
}

/* encryptMessage('Hello World!')
    .then((data) => {
        if (!data) {return}
        decryptMessage(data)
            .then((res) => {
                console.log('fin.');
            }).catch((error) => {
                console.error(error);
            });
    }).catch((error) => {
        console.error(error);
    }); */

//decryptMessage ("Z++89CkzfMHjoMCqKRtXixlfnciybulW1sAU8jOaaW8V9nr1BEY8rZQTVd4JES/jcjDktq9sDLfhnvlCOp03cEXjztIeua+mQ2gLUTQNaT5c3/S21KvX7NmZjyGd0ErkwX3kBxx6LpIpFHLBNJF8bVWDU4+se+uaXIw5mbi1Iy6MK7nDLQVujoJ1NM3km4Kru09cC4wuuP8BLIchOnGOy+chvd2kEngnYeJAtlUtnpD5ANdP531X/+VXDBgjaAKeyh2ltE0E+VbRbqPwCbvp8HA4TSRtCNw5hz+nmtMd0hSzdcTrH3gbF5YbHBlx2J5z32zcfkBQb0PurhnACWJX9Q==")
//encryptMessage('Hello World!')

//console.log(atob ("Upf0VDGsYLQjdmOJFIpEk7sRyStM0+Qn/ra3THLfT7KFuq2CtBzcdO9Svs1K/WLYcMckb92tz+NayIU2E20QZyGSpndb4ZAOkMIPY66brr51rVBWtIgU+scmm173wkPfRhOt5rc5HVlbRn6Dv++5GDElxKxOXdvNAO7jrx4Q9v/RbTraDQyt/TsLFQOhvvjxQvhkTl54CT01UvCVnSEsfEVv4aW/N1WLkTDmp3M1Jt8RHmYXoqQ79mUT7Pv9hjZl5tWgZXouHq87BCQhnWZRUdEE1I8SmH5GoZ9A4BQKYsyL02io7569Xs11E/UlvcyDRF3RvsQ43S4Z2sav/whICQ=="))
console.log (parseHexToText("48656c6c6f20576f726c6421"))