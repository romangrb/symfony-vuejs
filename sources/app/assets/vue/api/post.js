import axios from "axios";

export default {
  create(message) {
    return axios.post("/api/posts", {
      message: message
    });
  },
  findAll() {
    return axios.get("/api/posts");
  },
  delete(id) {
    return axios.delete(`/api/post/${id}`);
  },
};