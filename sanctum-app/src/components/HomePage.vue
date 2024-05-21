<template>
  <div>
    <nav class="navbar navbar-expand-lg custom-bg">
      <div class="container-fluid">
        <a class="navbar-brand text-white" href="#">Dashboard</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown"
          aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNavDropdown">
          <ul class="navbar-nav ms-auto">
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle text-white" href="#" id="navbarDropdownMenuLink" role="button"
                data-bs-toggle="dropdown" aria-expanded="false">
                User
              </a>
              <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdownMenuLink">
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
          <h5 class="card-title">Create a Post</h5>
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
          <div class="avatar-container me-3">
            <div class="avatar"></div>
          </div>
          <p v-if="editIndex !== index">{{ post.content }}</p>
          <input v-else v-model="editContent" class="form-control" />
          <p class="text-muted">Posted by: {{ post.author }}</p>
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
          <div class="comment mb-3" v-for="(comment, commentIndex) in post.comments" :key="commentIndex">
            <p>{{ comment.content }}</p>
            <p class="text-muted">Commented by: {{ comment.author }}</p>
            <button v-if="comment.user_id === userId || post.user_id === userId"
              @click="deleteComment(comment.id, post.id)" type="button" class="btn btn-sm btn-transparent">
              <img src="@/assets/DeleteIcon.png" alt="Delete Icon">
            </button>
          </div>
          <!-- Add Comment Form -->
          <div class="d-flex mb-3 align-items-center">
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

        // Ensure that each post has a comments array
        this.posts = response.data.posts.map(post => ({
          ...post,
          comments: post.comments || [],
          newComment: ''  // Add a property for new comments input
        }));

        this.userId = response.data.user_id;
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
          this.posts[postIndex].newComment = ''; // Clear the new comment input field
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
</style>
