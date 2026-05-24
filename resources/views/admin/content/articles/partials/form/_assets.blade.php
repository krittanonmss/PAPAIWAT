@once
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>

    <style>
        .temple-editor-toolbar.ql-toolbar {
            border: 0;
            display: flex;
            flex-wrap: wrap;
            gap: 0.375rem;
            align-items: center;
            font-family: inherit;
        }

        .temple-editor-toolbar.ql-toolbar .ql-formats {
            align-items: center;
            border-right: 1px solid rgb(255 255 255 / 0.1);
            display: inline-flex;
            gap: 0.125rem;
            margin: 0;
            padding-right: 0.375rem;
        }

        .temple-editor-toolbar.ql-toolbar .ql-formats:last-child {
            border-right: 0;
            padding-right: 0;
        }

        .temple-editor-toolbar.ql-toolbar button {
            border-radius: 0.5rem;
            color: rgb(203 213 225);
            height: 2rem;
            padding: 0.375rem;
            width: 2rem;
        }

        .temple-editor-toolbar.ql-toolbar button:hover,
        .temple-editor-toolbar.ql-toolbar button:focus,
        .temple-editor-toolbar.ql-toolbar button.ql-active {
            background: rgb(59 130 246 / 0.14);
            color: rgb(147 197 253);
        }

        .temple-editor-toolbar.ql-toolbar .ql-stroke {
            stroke: currentColor;
        }

        .temple-editor-toolbar.ql-toolbar .ql-fill {
            fill: currentColor;
        }

        .temple-editor-toolbar.ql-toolbar .ql-picker {
            color: rgb(203 213 225);
            height: 2rem;
        }

        .temple-editor-toolbar.ql-toolbar .ql-picker-label {
            align-items: center;
            border: 1px solid rgb(255 255 255 / 0.1);
            border-radius: 0.5rem;
            display: flex;
            min-width: 6.25rem;
            padding-left: 0.625rem;
        }

        .temple-editor-toolbar.ql-toolbar .ql-picker-label:hover,
        .temple-editor-toolbar.ql-toolbar .ql-picker-label.ql-active {
            border-color: rgb(96 165 250 / 0.6);
            color: rgb(147 197 253);
        }

        .temple-editor-toolbar.ql-toolbar .ql-picker-options {
            border: 1px solid rgb(255 255 255 / 0.12);
            border-radius: 0.75rem;
            background: rgb(15 23 42);
            box-shadow: 0 20px 40px rgb(2 6 23 / 0.45);
            color: rgb(226 232 240);
            margin-top: 0.375rem;
            padding: 0.375rem;
        }

        .temple-editor-toolbar.ql-toolbar .ql-picker-item {
            border-radius: 0.5rem;
            padding: 0.375rem 0.625rem;
        }

        .temple-editor-toolbar.ql-toolbar .ql-picker-item:hover,
        .temple-editor-toolbar.ql-toolbar .ql-picker-item.ql-selected {
            background: rgb(59 130 246 / 0.16);
            color: rgb(147 197 253);
        }

        .temple-editor-toolbar.ql-toolbar .ql-header .ql-picker-label::before,
        .temple-editor-toolbar.ql-toolbar .ql-header .ql-picker-item::before {
            content: 'Paragraph';
        }

        .temple-editor-toolbar.ql-toolbar .ql-header .ql-picker-label[data-value="1"]::before,
        .temple-editor-toolbar.ql-toolbar .ql-header .ql-picker-item[data-value="1"]::before {
            content: 'Heading 1';
        }

        .temple-editor-toolbar.ql-toolbar .ql-header .ql-picker-label[data-value="2"]::before,
        .temple-editor-toolbar.ql-toolbar .ql-header .ql-picker-item[data-value="2"]::before {
            content: 'Heading 2';
        }

        .temple-editor-toolbar.ql-toolbar .ql-header .ql-picker-label[data-value="3"]::before,
        .temple-editor-toolbar.ql-toolbar .ql-header .ql-picker-item[data-value="3"]::before {
            content: 'Heading 3';
        }

        .temple-editor-toolbar.ql-toolbar .ql-lineheight .ql-picker-label::before,
        .temple-editor-toolbar.ql-toolbar .ql-lineheight .ql-picker-item::before {
            content: 'Normal';
        }

        .temple-editor-toolbar.ql-toolbar .ql-lineheight .ql-picker-label[data-value="tight"]::before,
        .temple-editor-toolbar.ql-toolbar .ql-lineheight .ql-picker-item[data-value="tight"]::before {
            content: 'Tight';
        }

        .temple-editor-toolbar.ql-toolbar .ql-lineheight .ql-picker-label[data-value="relaxed"]::before,
        .temple-editor-toolbar.ql-toolbar .ql-lineheight .ql-picker-item[data-value="relaxed"]::before {
            content: 'Relaxed';
        }

        .temple-editor-toolbar.ql-toolbar .ql-lineheight .ql-picker-label[data-value="loose"]::before,
        .temple-editor-toolbar.ql-toolbar .ql-lineheight .ql-picker-item[data-value="loose"]::before {
            content: 'Loose';
        }

        .temple-rich-editor.ql-container {
            border: 0;
            font-family: inherit;
        }

        .temple-rich-editor .ql-editor {
            min-height: inherit;
            padding: 0;
            color: rgb(241 245 249);
            font-size: 1rem;
            line-height: 1.75;
        }

        .temple-rich-editor .ql-editor.ql-blank::before {
            color: rgb(100 116 139);
            font-style: normal;
            left: 0;
            right: 0;
        }

        .temple-rich-editor .ql-editor h1,
        .temple-rich-editor .ql-editor h2,
        .temple-rich-editor .ql-editor h3 {
            margin: 0.875rem 0 0.5rem;
            color: white;
            font-weight: 700;
        }

        .temple-rich-editor .ql-editor h1 {
            font-size: 1.5rem;
            line-height: 2rem;
        }

        .temple-rich-editor .ql-editor h2 {
            font-size: 1.25rem;
            line-height: 1.875rem;
        }

        .temple-rich-editor .ql-editor h3 {
            font-size: 1rem;
            line-height: 1.75rem;
        }

        .temple-rich-editor .ql-editor a {
            color: rgb(147 197 253);
            text-decoration: underline;
            text-underline-offset: 3px;
        }

        .temple-rich-editor .ql-editor blockquote {
            border-left: 3px solid rgb(96 165 250 / 0.6);
            color: rgb(203 213 225);
            padding-left: 0.875rem;
        }

        .temple-rich-editor .ql-editor .ql-code-block {
            border-radius: 0.75rem;
            background: rgb(2 6 23 / 0.85);
            color: rgb(203 213 225);
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
            padding: 0.125rem 0.75rem;
        }

        .temple-rich-editor .ql-editor .ql-lineheight-tight {
            line-height: 1.25;
        }

        .temple-rich-editor .ql-editor .ql-lineheight-normal {
            line-height: 1.5;
        }

        .temple-rich-editor .ql-editor .ql-lineheight-relaxed {
            line-height: 1.75;
        }

        .temple-rich-editor .ql-editor .ql-lineheight-loose {
            line-height: 2;
        }

        @for ($i = 1; $i <= 8; $i++)
            .temple-rich-editor .ql-editor .ql-indent-{{ $i }} {
                padding-left: {{ $i * 1.5 }}rem;
            }
        @endfor

        .article-form-ui input[type="text"],
        .article-form-ui input[type="number"],
        .article-form-ui input[type="datetime-local"],
        .article-form-ui input[type="file"],
        .article-form-ui input[type="url"],
        .article-form-ui select,
        .article-form-ui textarea {
            min-height: 3rem;
            font-size: 0.95rem;
        }

        .article-form-ui label {
            font-size: 0.95rem;
        }

        .article-studio-tab-content .article-panel:not(.article-panel-content),
        .article-studio-tab-taxonomy .article-panel:not(.article-panel-taxonomy),
        .article-studio-tab-media .article-panel:not(.article-panel-media),
        .article-studio-tab-seo .article-panel:not(.article-panel-seo),
        .article-studio-tab-publish .article-panel:not(.article-panel-publish) {
            display: none !important;
        }
    </style>
    @endonce
