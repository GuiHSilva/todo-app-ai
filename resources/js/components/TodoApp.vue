<template>
  <div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-6">Lista de Tarefas</h1>

    <!-- Formulário para adicionar -->
    <div class="mb-8 bg-white p-6 rounded-lg shadow-md">
      <h2 class="text-xl font-semibold mb-4">Adicionar Nova Tarefa</h2>
      <form @submit.prevent="addTodo" class="flex flex-col space-y-4">
        <div>
          <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Título</label>
          <input
            type="text"
            id="title"
            v-model="newTodo.title"
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
            required
          >
        </div>

        <div>
          <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Descrição</label>
          <textarea
            id="description"
            v-model="newTodo.description"
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
            rows="3"
          ></textarea>
        </div>

        <button
          type="submit"
          class="bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 transition-colors"
        >
          Adicionar Tarefa
        </button>
      </form>
    </div>

    <!-- Lista de tarefas -->
    <div class="bg-white p-6 rounded-lg shadow-md">
      <h2 class="text-xl font-semibold mb-4">Minhas Tarefas</h2>

      <div v-if="loading" class="text-center py-4">
        <p>Carregando...</p>
      </div>

      <div v-else-if="todos.length === 0" class="text-center py-4">
        <p>Nenhuma tarefa encontrada. Adicione sua primeira tarefa!</p>
      </div>

      <ul v-else class="divide-y divide-gray-200">
        <li v-for="todo in todos" :key="todo.id" class="py-4">
          <div class="flex items-start">
            <input
              type="checkbox"
              :checked="todo.completed"
              @change="toggleTodoStatus(todo)"
              class="mt-1 mr-3"
            >
            <div>
              <h3 class="text-lg font-medium" :class="{ 'line-through text-gray-500': todo.completed }">
                {{ todo.title }}
              </h3>
              <p class="text-gray-600 mt-1" v-if="todo.description">{{ todo.description }}</p>
            </div>
            <button
              @click="deleteTodo(todo)"
              class="ml-auto text-red-600 hover:text-red-800"
            >
              Excluir
            </button>
          </div>
        </li>
      </ul>
    </div>
  </div>
</template>

<script>
// Imports
import axios from 'axios';

export default {
  data() {
    return {
      todos: [],
      loading: true,
      newTodo: {
        title: '',
        description: '',
        completed: false
      }
    };
  },

  mounted() {
    this.fetchTodos();
  },

  methods: {
    fetchTodos() {
      this.loading = true;
      axios.get('/api/todos')
        .then(response => {
          this.todos = response.data;
        })
        .catch(error => {
          console.error('Erro ao buscar tarefas:', error);
          alert('Não foi possível carregar as tarefas. Por favor, tente novamente.');
        })
        .finally(() => {
          this.loading = false;
        });
    },

    addTodo() {
      axios.post('/api/todos', this.newTodo)
        .then(response => {
          this.todos.push(response.data);
          this.newTodo.title = '';
          this.newTodo.description = '';
        })
        .catch(error => {
          console.error('Erro ao adicionar tarefa:', error);
          alert('Não foi possível adicionar a tarefa. Por favor, tente novamente.');
        });
    },

    toggleTodoStatus(todo) {
      const updatedTodo = {
        ...todo,
        completed: !todo.completed
      };

      axios.put(`/api/todos/${todo.id}`, updatedTodo)
        .then(response => {
          const index = this.todos.findIndex(t => t.id === todo.id);
          this.todos[index] = response.data;
        })
        .catch(error => {
          console.error('Erro ao atualizar tarefa:', error);
          alert('Não foi possível atualizar a tarefa. Por favor, tente novamente.');
        });
    },

    deleteTodo(todo) {
      if (confirm('Tem certeza que deseja excluir esta tarefa?')) {
        axios.delete(`/api/todos/${todo.id}`)
          .then(() => {
            this.todos = this.todos.filter(t => t.id !== todo.id);
          })
          .catch(error => {
            console.error('Erro ao excluir tarefa:', error);
            alert('Não foi possível excluir a tarefa. Por favor, tente novamente.');
          });
      }
    }
  }
};
</script>