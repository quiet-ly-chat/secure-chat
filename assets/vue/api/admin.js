import axios from 'axios';

export default {
    getUsernames() {
        return axios.post(
            '/api/admin/username',
        );
    },
    addUsername(username) {
        return axios.post(
            '/api/admin/username/new',
            {
                username: username
            }
        );
    },
    removeUsername(username) {
        return axios.delete('/api/admin/username/' + username)
    }
}