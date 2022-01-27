import { createStore } from 'vuex'

const store = createStore({
    state() {
        return {
            showSuccessFlash: false,
            showErrorFlash: false,
        }
    },
    getters: {
        successFlashMessage(state) {
            return state.showSuccessFlash;
        }
    },
    mutations: {
        successFlashMessage(state, status) {
            state.showSuccessFlash = status;
        }
    },
    actions: {
        setSuccessFlashMessage({ commit }, status) {
            commit('successFlashMessage', status);
        }
    }
});


export default store;