import axios from 'axios';

export default {
    login(login, password) {
        return axios.post(
            '/api/security/login',
            {
                username: login,
                password: password
            }
        );
    },
    register(registerData) {
        return axios.post(
            '/api/security/register',
            {
                email: registerData.login + '@quitly.com',
                username: registerData.login,
                plainPassword: {
                    first: registerData.password,
                    second: registerData.confirmPassword
                },
                privateKey: registerData.privateKey,
                publicKey: registerData.publicKey,
            }
        );
    },
}