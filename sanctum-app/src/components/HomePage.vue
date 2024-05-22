<template>
  <div>
    <nav class="navbar navbar-expand-lg custom-bg">
      <div class="container-fluid">
        <a class="navbar-brand text-white" href="#">Home</a>
        <a class="navbar-brand text-white" href="#">Forums</a>
        <a class="navbar-brand text-white" href="#">What's New</a>
        <a class="navbar-brand text-white" href="#">Chat</a>
        <a class="navbar-brand text-white" href="#">Media</a>
        <a class="navbar-brand text-white" href="#">Members</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown"
          aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNavDropdown">
          <ul class="navbar-nav ms-auto">
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle text-white" href="#" id="navbarDropdownMenuLink" role="button"
                data-bs-toggle="dropdown" aria-expanded="false">
                <img src="@/assets/profile.png" alt="Profile Icon" class="profile-icon me-2" />
                {{ userName }} <!-- Display user's name dynamically -->
              </a>
              <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdownMenuLink">
                <li>
                  <router-link class="dropdown-item">Profile</router-link>
                </li>
                <li>
                  <router-link class="dropdown-item">Setting</router-link>
                </li>
                <li>
                  <router-link to="/logout" class="dropdown-item">Logout</router-link>
                </li>
              </ul>
            </li>
          </ul>
        </div>
      </div>
    </nav>
    <div class="container mt-4">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title"> Create a Post</h5>
          <div class="mb-3">
            <textarea v-model="postContent" class="form-control" id="postContent" rows="3"
              placeholder="What's on your mind?"></textarea>
          </div>
          <div class="d-flex justify-content-end">
            <button @click="createPost" type="button" class="btn btn-primary">Post</button>
          </div>
        </div>
      </div>
    </div>
    <div class="container mt-4">
      <div v-for="(post, index) in posts" :key="index" class="card mb-3">
        <div class="card-body">
          <p v-if="editIndex !== index"> <img src="@/assets/profile.png" alt="Profile Icon" class="profile-icon me-2" />
            <b>{{ post.author }}</b>
            <br>
            <small class="author-date smaller">{{ formatDateTime(post.created_at) }}</small>
          </p>
          <input v-else v-model="editContent" class="form-control"/>
          <p class="text-muted">{{ post.content }}</p>
          <div class="d-flex justify-content-end">
            <button v-if="editIndex === index" @click="updatePost(post.id)" type="button"
              class="btn btn-sm btn-primary">Save</button>
            <button v-if="editIndex === index" @click="cancelEdit" type="button"
              class="btn btn-sm btn-secondary">Cancel</button>
            <button v-if="post.user_id === userId" @click="startEdit(post, index)" type="button"
              class="btn btn-sm btn-transparent">
              <img src="@/assets/EditIcon.png" alt="Edit Icon">
            </button>
            <button v-if="post.user_id === userId" @click="deletePost(post.id, index)" type="button"
              class="btn btn-sm btn-transparent">
              <img src="@/assets/DeleteIcon.png" alt="Delete Icon">
            </button>
          </div>
        </div>
        <!-- Comment Section -->
        <div class="card-footer">
          <h6 class="card-title">Comments</h6>
          <br>
          <div class="comment mb-3" v-for="(comment, commentIndex) in post.comments" :key="commentIndex">
            <p
              v-if="!(editCommentIndex && editCommentIndex.postIndex === index && editCommentIndex.commentIndex === commentIndex)">
              <img src="@/assets/happiness.png" alt="Profile Icon" class="profile-icon me-2" />
              <b>{{ comment.author }}</b>
              <br>
              <small class="author-date smaller">{{ formatDateTime(comment.created_at) }}</small>
            </p>
            <input v-else v-model="editCommentContent" class="form-control" />
            <p class="text-muted">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ comment.content }}</p>
            <div class="d-flex justify-content">
              <button
                v-if="editCommentIndex && editCommentIndex.postIndex === index && editCommentIndex.commentIndex === commentIndex"
                @click="updateComment(comment.id, index, commentIndex)" type="button"
                class="btn btn-sm btn-primary">Save</button>
              <button
                v-if="editCommentIndex && editCommentIndex.postIndex === index && editCommentIndex.commentIndex === commentIndex"
                @click="cancelEditComment" type="button" class="btn btn-sm btn-secondary">Cancel</button>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <button v-if="comment.user_id === userId" @click="startEditComment(comment, index, commentIndex)"
                type="button" class="btn btn-sm btn-transparent">
                <img src="@/assets/EditIcon.png" alt="Edit Icon" height="17px">
              </button>
              <button v-if="comment.user_id === userId || post.user_id === userId"
                @click="deleteComment(comment.id, post.id)" type="button" class="btn btn-sm btn-transparent">
                <img src="@/assets/DeleteIcon.png" alt="Delete Icon" height="17px">
              </button>
            </div>
          </div>
          <!-- Add Comment Form -->
          <div class="d-flex mb-3 align-items-center">
            <img src="@/assets/ulo.png" alt="Profile Icon" class="profile-icon me-2" />
            <textarea v-model="post.newComment" class="form-control me-2" rows="1"
              placeholder="Add a comment"></textarea>
            <button @click="addComment(post.id, post.newComment)" type="button"
              class="btn btn-sm btn-primary">Submit</button>
          </div>
        </div>

      </div>

    </div>

  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'HomePage',
  data() {
    return {
      postContent: '',
      posts: [],
      newComment: '',
      editIndex: null,
      editContent: '',
      userId: null,
      editCommentIndex: null,
      editCommentContent: '',
      userName: '',
    };
  },
  methods: {
    async createPost() {
      try {
        const response = await axios.post(`${this.$root.$data.apiUrl}/posts`, {
          content: this.postContent
        }, {
          headers: {
            'Authorization': `Bearer ${localStorage.getItem('token')}`
          }
        });
        this.posts.push(response.data);
        this.postContent = '';
      } catch (error) {
        console.error('Error creating post:', error);
      }
    },
    async fetchPosts() {
      try {
        const response = await axios.get(`${this.$root.$data.apiUrl}/posts`, {
          headers: {
            Authorization: `Bearer ${localStorage.getItem('token')}`,
          },
        });


        this.posts = response.data.posts.map(post => ({
          ...post,
          comments: post.comments || [],
          newComment: ''
        }));

        this.userId = response.data.user_id;
        this.userName = response.data.user_name; // Set the user's name from the response
      } catch (error) {
        console.error('Error fetching posts:', error);
      }
    },
    async addComment(postId, commentContent) {
      try {
        const response = await axios.post(`${this.$root.$data.apiUrl}/comments`, {
          post_id: postId,
          content: commentContent,
        }, {
          headers: {
            'Authorization': `Bearer ${localStorage.getItem('token')}`
          }
        });

        const postIndex = this.posts.findIndex(post => post.id === postId);
        if (postIndex !== -1) {
          this.posts[postIndex].comments.push(response.data);
          this.posts[postIndex].newComment = '';
        }

      } catch (error) {
        console.error('Error adding comment:', error);
      }
    },
    async deleteComment(commentId, postId) {
      try {
        await axios.delete(`${this.$root.$data.apiUrl}/comments/${commentId}`, {
          headers: {
            Authorization: `Bearer ${localStorage.getItem('token')}`
          }
        });
        const post = this.posts.find(post => post.id === postId);
        const commentIndex = post.comments.findIndex(comment => comment.id === commentId);
        post.comments.splice(commentIndex, 1);
      } catch (error) {
        console.error('Error deleting comment:', error);
      }
    },
    startEdit(post, index) {
      this.editIndex = index;
      this.editContent = post.content;
    },
    cancelEdit() {
      this.editIndex = null;
      this.editContent = '';
    },
    async updatePost(postId) {
      try {
        const response = await axios.put(`${this.$root.$data.apiUrl}/posts/${postId}`, {
          content: this.editContent
        }, {
          headers: {
            Authorization: `Bearer ${localStorage.getItem('token')}`
          }
        });
        this.posts[this.editIndex].content = response.data.post.content;
        this.cancelEdit();
      } catch (error) {
        console.error('Error updating post:', error);
      }
    },
    async deletePost(postId, index) {
      try {
        await axios.delete(`${this.$root.$data.apiUrl}/posts/${postId}`, {
          headers: {
            Authorization: `Bearer ${localStorage.getItem('token')}`
          }
        });
        this.posts.splice(index, 1);
      } catch (error) {
        console.error('Error deleting post:', error);
      }
    },
    startEditComment(comment, postIndex, commentIndex) {
      this.editCommentIndex = { postIndex, commentIndex };
      this.editCommentContent = comment.content;
    },
    cancelEditComment() {
      this.editCommentIndex = null;
      this.editCommentContent = '';
    },
    async updateComment(commentId, postIndex, commentIndex) {
      try {
        const response = await axios.put(`${this.$root.$data.apiUrl}/comments/${commentId}`, {
          content: this.editCommentContent
        }, {
          headers: {
            Authorization: `Bearer ${localStorage.getItem('token')}`
          }
        });
        this.posts[postIndex].comments[commentIndex].content = response.data.content;
        this.cancelEditComment();
      } catch (error) {
        console.error('Error updating comment:', error);
      }
    },
    formatDateTime(dateTime) {
    const date = new Date(dateTime);
    const hours = date.getHours();
    const minutes = date.getMinutes();
    const meridiem = hours >= 12 ? 'PM' : 'AM';
    const formattedHours = hours % 12 || 12;
    const formattedMinutes = minutes < 10 ? '0' + minutes : minutes;
    const formattedDate = `${formattedHours}:${formattedMinutes} ${meridiem} | ${date.getMonth() + 1}-${date.getDate()}-${date.getFullYear()}`;
    return formattedDate;
  }
  },
  mounted() {
    this.fetchPosts();
  },
};
</script>

<style>
.custom-bg {
  background-color: #1d1927;
}

.text-white {
  color: white !important;
}

.card {
  margin-top: 20px;
}

.btn-primary {
  background-color: #007bff;
  border-color: #007bff;
}

textarea.form-control {
  resize: none;
}

.profile-icon {
  width: 30px;
  height: 30px;
  border-radius: 50%;
}

.smaller {
    font-size: 0.60em; /* Adjust the size as needed */
}

.author-date {
    margin-left: 38px; /* Adjust horizontal spacing as needed */
    margin-top: -8px;  /* Adjust vertical spacing as needed */
    display: block;  /* Ensure it behaves like a block element to handle vertical spacing */
    opacity: 50%;
}
</style>
