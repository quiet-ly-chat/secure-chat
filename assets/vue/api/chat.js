import axios from 'axios';

export default {
    getFriends() {
        return axios.get(
            '/api/friend',
        );
    },
    addNewFriend(friend) {
        return axios.post(
            '/api/friend/new',
            {
                username: friend
            }
        );
    },
    removeFriendship(friend) {
        return axios.delete('/api/friend/' + friend)
    },
    getMessagesForFriend(friendId) {
        return axios.get('/api/message/' + friendId,
        );
    },
    getMessagesForAll() {
        return axios.get('/api/message');
    }
}