<template>
  <vue-pdf-app
   class="handbook-pdf"
    :pdf="pdf"
    theme="light"
    :file-name="fileName"
    :config="config"
  />
</template>

<script>
import VuePdfApp from "vue-pdf-app";
import "vue-pdf-app/dist/icons/main.css";

export default {
  name: "HandbookPdf",
  props: {
    pdf: {
      default() {
        return null;
      }
    },
    fileName: {
      type: String,
      default() {
        return 'Довідник системи каталог навчальних планів'
      }
    },
    config: {
      type: Object,
      default() {
        return {
          toolbar: {
            toolbarViewerRight: {
              openFile: false,
              print: false,
              viewBookmark: false,
            }
          }
        };
      }
    }
  },
  components: {
    VuePdfApp
  },
}
</script>

<style>
.handbook-pdf.pdf-app #thumbnailView {
  width: 100%;
}
.handbook-pdf.pdf-app {
  z-index: 1;
  position: relative;
  height: 100vh;
}
</style>
