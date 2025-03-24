const { BakongKHQR } = require("bakong-khqr");

const action = process.argv[2];
const qrCode = process.argv[3];

async function verify(qrCode) {
    try {
        const KHQR = new BakongKHQR();
        const response = KHQR.verify(qrCode);

        if (response.Status.Code === 0) {
            console.log(JSON.stringify({ code: 0, valid: response.Data.Valid, message: "KHQR is Valid" }));
            console.log(JSON.stringify({ code: 1, message: response.Status.Message }));
        }
    } catch (error) {
        console.log(JSON.stringify({ code: 1, message: error.message }));
    }
}

async function decode(qrCode) {
    try {
        const KHQR = new BakongKHQR();
        const response = KHQR.decode(qrCode);

        if (response.Status.Code === 0) {
            console.log(JSON.stringify({ code: 0, data: response.Data, message: "KHQR Decoded Successfully" }));
        } else {
            console.log(JSON.stringify({ code: 1, message: response.Status.Message }));
        }
    } catch (error) {
        console.log(JSON.stringify({ code: 1, message: error.message }));
    }
}


if (action === 'verify') {
    verify(qrCode);
} else if (action === 'decode') {
    decode(qrCode);
} else {
    console.log(JSON.stringify({ code: 1, message: "Invalid action" }));
}