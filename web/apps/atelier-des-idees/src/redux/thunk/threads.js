import { removeThread, toggleApproveThread } from '../actions/threads';
import { createRequest, createRequestSuccess, createRequestFailure } from '../actions/loading';
import { POST_THREAD, POST_THREAD_COMMENT } from '../constants/actionTypes';
import { selectThread } from '../selectors/threads';

export function approveComment(id, parentId = '') {
    return (dispatch, getState, axios) => {
        let type = 'threads';
        if (parentId) {
            type = 'thread_comments';
        }
        // TODO: handle threadcomment
        const thread = selectThread(getState(), id);
        // simulate toggle
        dispatch(toggleApproveThread(id));
        return (
            axios
                .put(`/api/${type}/${id}/approval-toggle`, { approved: !thread.approved })
                // toggle back if error
                .catch(() => dispatch(toggleApproveThread(id)))
        );
    };
}

export function deleteComment(id, parentId = '') {
    return (dispatch, getState, axios) => {
        let type = 'threads';
        if (parentId) {
            type = 'thread_comments';
        }
        return axios
            .delete(`/api/${type}/${id}`)
            .then(() => dispatch(removeThread(id)))
            .catch((error) => {
                throw error;
            });
    };
}

export function postComment(content, answerId, parentId = '') {
    return (dispatch, getState, axios) => {
        let type = 'threads';
        const fetchType = parentId ? POST_THREAD_COMMENT : POST_THREAD;
        const fetchId = `${answerId}${parentId ? `_${parentId}` : ''}`;
        const body = { content };
        if (parentId) {
            type = 'thread_comments';
            body.thread = parentId;
        } else {
            body.answer = answerId;
        }
        dispatch(createRequest(fetchType, fetchId));
        return axios
            .post(`/api/${type}`, body)
            .then(res => res.data)
            .then((thread) => {
                dispatch(createRequestSuccess(fetchType, fetchId));
                return thread;
            })
            .catch((error) => {
                dispatch(createRequestFailure(fetchType, fetchId));
                throw error;
            });
    };
}

export function reportComment(reportData, id, parentId = '') {
    return (dispatch, getState, axios) => {
        let type = 'threads';
        if (parentId) {
            type = 'thread_comments';
        }
        const reportType = parentId ? 'atelier-des-idees-reponses' : 'atelier-des-idees-commentaires';
        return axios.post(`/api/report/${reportType}/${id}`, reportData);
    };
}

export function fetchThreads(params = {}) {
    return (dispatch, getState, axios) =>
        axios
            .get('/api/threads', { params })
            .then(res => res.data)
            .catch((error) => {
                throw error;
            });
}
