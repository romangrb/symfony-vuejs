import axios from 'axios';

const apiUrl = process.env.API_URL;

const http = {
    get: function(url, params = {}) {
        return axios.get(apiUrl + url, params)
            .then(data => {
                return data.data;
            })
            .catch((error) => {
                throw error;
            });
    },
    patch: function(url, params = {}) {
        return axios.patch(apiUrl + url, params)
            .then(data => {
                return data.data;
            })
            .catch((error) => {
                throw error;
            });
    },
    post: function(url, params = {}) {
        return axios.post(apiUrl + url, params)
            .then(data => {
                return data.data;
            })
            .catch((error) => {
                throw error;
            });
    }
};

export default http;