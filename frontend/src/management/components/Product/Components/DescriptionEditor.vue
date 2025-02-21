<script setup>
import { EditorContent, useEditor } from "@tiptap/vue-3";
import StarterKit from "@tiptap/starter-kit";
import { Markdown } from "tiptap-markdown";
import { onBeforeUnmount, watch } from "vue";
import { Underline } from "@tiptap/extension-underline";
import { Image } from "@tiptap/extension-image";
import { TextAlign } from "@tiptap/extension-text-align";

const props = defineProps({
  modelValue: null,
});

const emit = defineEmits(["update:modelValue"]);

const editor = useEditor({
  content: props.modelValue || "",
  extensions: [
    StarterKit.configure({
      heading: {
        levels: [2, 3, 4],
      },
      code: false,
      codeBlock: false,
    }),
    Markdown.configure({
      transformPastedText: true,
      rules: {
        heading: true,
      },
    }),
    Underline,
    Image.configure({
      inline: true,
    }),
    TextAlign.configure({
      types: ["heading", "paragraph"],
      alignments: ["left", "right", "center"],
    }),
  ],
  editorProps: {
    attributes: {
      class:
        "prose prose-sm max-w-none p-4 rounded-bl-lg rounded-br-lg focus:ring-inset focus:outline-none focus-visible:ring-2 focus-visible:ring-yellow-500",
    },
  },
  onUpdate: () =>
    emit("update:modelValue", editor.value?.storage.markdown.getMarkdown()),
});

function setImage() {
  const url = window.prompt("URL");
  if (url) {
    return editor.value.chain().setImage({ src: url }).run();
  }
}

onBeforeUnmount(() => {
  editor.value?.destroy();
});

watch(
  () => props.modelValue,
  (value) => {
    if (value === editor.value?.storage.markdown.getMarkdown()) {
      return;
    }
    editor.value?.commands.setContent(value, true);
  },
  { immediate: true },
);
</script>

<template>
  <div
    v-if="editor"
    class="rounded-lg bg-white dark:bg-gray-200 text-left shadow-md border-0"
  >
    <menu class="flex divide-x-2 border-b-2 flex-wrap">
      <li>
        <button
          @click="() => editor.chain().toggleBold().run()"
          type="button"
          class="px-3 py-2 rounded-tl-lg"
          :class="[
            editor.isActive('bold')
              ? 'bg-yellow-700 text-white'
              : 'hover:bg-yellow-500',
          ]"
          title="Bold"
        >
          <i class="fa-solid fa-bold dark:text-black"></i>
        </button>
      </li>
      <li>
        <button
          @click="() => editor.chain().toggleItalic().run()"
          type="button"
          class="px-3 py-2"
          :class="[
            editor.isActive('italic')
              ? 'bg-yellow-700 text-white'
              : 'hover:bg-yellow-500',
          ]"
          title="Italic"
        >
          <i class="fa-solid fa-italic dark:text-black"></i>
        </button>
      </li>
      <li>
        <button
          @click="() => editor.chain().toggleUnderline().run()"
          type="button"
          class="px-3 py-2"
          :class="[
            editor.isActive('underline')
              ? 'bg-yellow-700 text-white'
              : 'hover:bg-yellow-500',
          ]"
          title="Underline"
        >
          <i class="fa-solid fa-underline dark:text-black"></i>
        </button>
      </li>
      <li>
        <button
          @click="() => editor.chain().toggleBlockquote().run()"
          type="button"
          class="px-3 py-2"
          :class="[
            editor.isActive('blockquote')
              ? 'bg-yellow-700 text-white'
              : 'hover:bg-yellow-500',
          ]"
          title="Blockquote"
        >
          <i class="fa-solid fa-quote-left dark:text-black"></i>
        </button>
      </li>
      <li>
        <button
          @click="() => editor.chain().toggleBulletList().run()"
          type="button"
          class="px-3 py-2"
          :class="[
            editor.isActive('bulletList')
              ? 'bg-yellow-700 text-white'
              : 'hover:bg-yellow-500',
          ]"
          title="Bullet List"
        >
          <i class="fa-solid fa-list-ul dark:text-black"></i>
        </button>
      </li>
      <li>
        <button
          @click="() => editor.chain().toggleOrderedList().run()"
          type="button"
          class="px-3 py-2"
          :class="[
            editor.isActive('orderedList')
              ? 'bg-yellow-700 text-white'
              : 'hover:bg-yellow-500',
          ]"
          title="Numeric List"
        >
          <i class="fa-solid fa-list-ol dark:text-black"></i>
        </button>
      </li>
      <li>
        <button
          @click="() => editor.chain().setHorizontalRule().run()"
          type="button"
          class="px-3 py-2"
          :class="[
            editor.isActive('horizontalRule')
              ? 'bg-yellow-700 text-white'
              : 'hover:bg-yellow-500',
          ]"
          title="Horizontal Rule"
        >
          Horizontal Rule
        </button>
      </li>
      <li>
        <button
          @click="() => editor.chain().toggleHeading({ level: 2 }).run()"
          type="button"
          class="px-3 py-2 h-full"
          :class="[
            editor.isActive('heading', { level: 2 })
              ? 'bg-yellow-700 text-white'
              : 'hover:bg-yellow-500',
          ]"
          title="Heading 1"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24"
            stroke-width="1.5"
            stroke="currentColor"
            class="size-5 dark:text-black"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              d="M2.243 4.493v7.5m0 0v7.502m0-7.501h10.5m0-7.5v7.5m0 0v7.501m4.501-8.627 2.25-1.5v10.126m0 0h-2.25m2.25 0h2.25"
            />
          </svg>
        </button>
      </li>
      <li>
        <button
          @click="() => editor.chain().toggleHeading({ level: 3 }).run()"
          type="button"
          class="px-3 py-2 h-full"
          :class="[
            editor.isActive('heading', { level: 3 })
              ? 'bg-yellow-700 text-white'
              : 'hover:bg-yellow-500',
          ]"
          title="Heading 2"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24"
            stroke-width="1.5"
            stroke="currentColor"
            class="size-5 dark:text-black"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              d="M21.75 19.5H16.5v-1.609a2.25 2.25 0 0 1 1.244-2.012l2.89-1.445c.651-.326 1.116-.955 1.116-1.683 0-.498-.04-.987-.118-1.463-.135-.825-.835-1.422-1.668-1.489a15.202 15.202 0 0 0-3.464.12M2.243 4.492v7.5m0 0v7.502m0-7.501h10.5m0-7.5v7.5m0 0v7.501"
            />
          </svg>
        </button>
      </li>
      <li>
        <button
          @click="() => editor.chain().toggleHeading({ level: 4 }).run()"
          type="button"
          class="px-3 py-2 h-full"
          :class="[
            editor.isActive('heading', { level: 4 })
              ? 'bg-yellow-700 text-white'
              : 'hover:bg-yellow-500',
          ]"
          title="Heading 3"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24"
            stroke-width="1.5"
            stroke="currentColor"
            class="size-5 dark:text-black"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              d="M20.905 14.626a4.52 4.52 0 0 1 .738 3.603c-.154.695-.794 1.143-1.504 1.208a15.194 15.194 0 0 1-3.639-.104m4.405-4.707a4.52 4.52 0 0 0 .738-3.603c-.154-.696-.794-1.144-1.504-1.209a15.19 15.19 0 0 0-3.639.104m4.405 4.708H18M2.243 4.493v7.5m0 0v7.502m0-7.501h10.5m0-7.5v7.5m0 0v7.501"
            />
          </svg>
        </button>
      </li>
      <li>
        <button
          @click="setImage"
          type="button"
          class="px-3 py-2 h-full"
          :class="[
            editor.isActive('image')
              ? 'bg-yellow-700 text-white'
              : 'hover:bg-yellow-500',
          ]"
          title="Set Image"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24"
            stroke-width="1.5"
            stroke="currentColor"
            class="size-5 dark:text-black"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z"
            />
          </svg>
        </button>
      </li>
      <li>
        <button
          @click="() => editor.chain().setTextAlign('left').run()"
          type="button"
          class="px-3 py-2 h-full"
          :class="[
            editor.isActive('textAlign', { textAlign: 'left' })
              ? 'bg-yellow-700 text-white'
              : 'hover:bg-yellow-500',
          ]"
          title="Text Left"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24"
            stroke-width="1.5"
            stroke="currentColor"
            class="size-5 dark:text-black"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25H12"
            />
          </svg>
        </button>
      </li>
      <li>
        <button
          @click="() => editor.chain().setTextAlign('center').run()"
          type="button"
          class="px-3 py-2 h-full"
          :class="[
            editor.isActive('textAlign', { textAlign: 'center' })
              ? 'bg-yellow-700 text-white'
              : 'hover:bg-yellow-500',
          ]"
          title="Text Center"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24"
            stroke-width="1.5"
            stroke="currentColor"
            class="size-5 dark:text-black"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"
            />
          </svg>
        </button>
      </li>
      <li>
        <button
          @click="() => editor.chain().setTextAlign('right').run()"
          type="button"
          class="px-3 py-2 h-full"
          :class="[
            editor.isActive('textAlign', { textAlign: 'right' })
              ? 'bg-yellow-700 text-white'
              : 'hover:bg-yellow-500',
          ]"
          title="Text Right"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24"
            stroke-width="1.5"
            stroke="currentColor"
            class="size-5 dark:text-black"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              d="M3.75 6.75h16.5M3.75 12h16.5M12 17.25h8.25"
            />
          </svg>
        </button>
      </li>
    </menu>
    <EditorContent :editor="editor"></EditorContent>
  </div>
</template>

<style scoped></style>
