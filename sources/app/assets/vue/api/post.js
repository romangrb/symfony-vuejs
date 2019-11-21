import axios from "axios";

export default {
  create(message) {
    return axios.post("/api/post", {
      message: message
    });
  },
  update(payload) {
    return axios.put(`/api/post/${payload.id}`, {
      message: payload.message
    });
  },
  findAll() {
    return axios.get("/api/posts");
  },
  delete(id) {
    return axios.delete(`/api/post/${id}`);
  },
};