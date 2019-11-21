import PostAPI from "../api/post";

const
  CREATING_POST = "CREATING_POST",
  CREATING_POST_SUCCESS = "CREATING_POST_SUCCESS",
  CREATING_POST_ERROR = "CREATING_POST_ERROR",
  UPDATING_POST = "UPDATING_POST",
  UPDATING_POST_SUCCESS = "UPDATING_POST_SUCCESS",
  UPDATING_POST_ERROR = "UPDATING_POST_ERROR",
  DELETING_POST = "DELETING_POST",
  DELETING_POST_SUCCESS = "DELETING_POST_SUCCESS",
  DELETING_POST_ERROR = "DELETING_POST_ERROR",
  FETCHING_POSTS = "FETCHING_POSTS",
  FETCHING_POSTS_SUCCESS = "FETCHING_POSTS_SUCCESS",
  FETCHING_POSTS_ERROR = "FETCHING_POSTS_ERROR";

export default {
  namespaced: true,
  state: {
    isLoading: false,
    error: null,
    posts: []
  },
  getters: {
    isLoading(state) {
      return state.isLoading;
    },
    hasError(state) {
      return state.error !== null;
    },
    error(state) {
      return state.error;
    },
    hasPosts(state) {
      return state.posts.length > 0;
    },
    posts(state) {
      return state.posts;
    }
  },
  mutations: {
    [CREATING_POST](state) {
      state.isLoading = true;
      state.error = null;
    },
    [UPDATING_POST](state) {
      state.isLoading = true;
      state.error = null;
    },
    [DELETING_POST](state) {
      state.isLoading = true;
      state.error = null;
    },
    [CREATING_POST_SUCCESS](state, post) {
      state.isLoading = false;
      state.error = null;
      state.posts.unshift(post);
    },
    [UPDATING_POST_SUCCESS](state) {
      state.isLoading = false;
      state.error = null;
    },
    [DELETING_POST_SUCCESS](state) {
      state.isLoading = false;
      state.error = null;
    },
    [CREATING_POST_ERROR](state, error) {
      state.isLoading = false;
      state.error = error;
      state.posts = [];
    },
    [UPDATING_POST_ERROR](state, error) {
      state.isLoading = false;
      state.error = error;
      state.posts = [];
    },
    [DELETING_POST_ERROR](state, error) {
      state.isLoading = false;
      state.error = error;
      state.posts = [];
    },
    [FETCHING_POSTS](state) {
      state.isLoading = true;
      state.error = null;
      state.posts = [];
    },
    [FETCHING_POSTS_SUCCESS](state, posts) {
      state.isLoading = false;
      state.error = null;
      state.posts = posts;
    },
    [FETCHING_POSTS_ERROR](state, error) {
      state.isLoading = false;
      state.error = error;
      state.posts = [];
    }
  },
  actions: {
    async create({ commit }, message) {
      commit(CREATING_POST);
      try {
        let response = await PostAPI.create(message);
        commit(CREATING_POST_SUCCESS, response.data);
        return response.data;
      } catch (error) {
        commit(CREATING_POST_ERROR, error);
        return null;
      }
    },
    async update({ dispatch, commit }, payload) {
      commit(UPDATING_POST);
      try {
        let response = await PostAPI.update(payload);
        commit(UPDATING_POST_SUCCESS, response.data);
      } catch (error) {
        commit(UPDATING_POST_ERROR, error);
      }
      return dispatch('findAll');
    },
    async delete({ dispatch, commit }, id) {
      commit(DELETING_POST);

      try {
        await PostAPI.delete(id).then();
        commit(DELETING_POST_SUCCESS);
      } catch (error) {
        commit(DELETING_POST_ERROR, error);
      }
      return dispatch('findAll');
    },
    async findAll({ commit }) {
      commit(FETCHING_POSTS);
      try {
        let response = await PostAPI.findAll();
        commit(FETCHING_POSTS_SUCCESS, response.data);
        return response.data;
      } catch (error) {
        commit(FETCHING_POSTS_ERROR, error);
        return null;
      }
    }
  }
};
