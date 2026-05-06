@once
    <style>
        .temple-rich-content {
            overflow-wrap: anywhere;
            line-height: 1.5;
        }

        .temple-rich-content h1,
        .temple-rich-content h2,
        .temple-rich-content h3 {
            margin: 1rem 0 0.5rem;
            font-weight: 700;
            line-height: 1.35;
        }

        .temple-rich-content h1 {
            font-size: 1.875rem;
        }

        .temple-rich-content h2 {
            font-size: 1.5rem;
        }

        .temple-rich-content h3 {
            font-size: 1.125rem;
        }

        .temple-rich-content p,
        .temple-rich-content ul,
        .temple-rich-content ol,
        .temple-rich-content blockquote,
        .temple-rich-content .ql-code-block {
            margin-bottom: 0.25rem;
        }

        .temple-rich-content ul,
        .temple-rich-content ol {
            padding-left: 1.35rem;
        }

        .temple-rich-content ul {
            list-style: disc;
        }

        .temple-rich-content ol {
            list-style: decimal;
        }

        .temple-rich-content li {
            margin: 0.125rem 0;
        }

        .temple-rich-content blockquote {
            border-left: 3px solid currentColor;
            opacity: 0.9;
            padding-left: 1rem;
        }

        .temple-rich-content a {
            text-decoration: underline;
            text-underline-offset: 3px;
        }

        .temple-rich-content code,
        .temple-rich-content .ql-code-block {
            border-radius: 0.75rem;
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
            font-size: 0.875em;
        }

        .temple-rich-content code {
            padding: 0.1rem 0.35rem;
        }

        .temple-rich-content .ql-code-block {
            display: block;
            padding: 0.25rem 0.875rem;
            white-space: pre-wrap;
        }

        .temple-rich-content .ql-lineheight-tight {
            line-height: 1.25;
        }

        .temple-rich-content .ql-lineheight-normal {
            line-height: 1.5;
        }

        .temple-rich-content .ql-lineheight-relaxed {
            line-height: 1.75;
        }

        .temple-rich-content .ql-lineheight-loose {
            line-height: 2;
        }

        @for ($i = 1; $i <= 8; $i++)
            .temple-rich-content .ql-indent-{{ $i }} {
                padding-left: {{ $i * 1.5 }}rem;
            }
        @endfor

        .temple-rich-content-dark h1,
        .temple-rich-content-dark h2,
        .temple-rich-content-dark h3 {
            color: #ffffff;
        }

        .temple-rich-content-dark a {
            color: #93c5fd;
        }

        .temple-rich-content-dark blockquote {
            border-color: rgb(96 165 250 / 0.7);
            color: #cbd5e1;
        }

        .temple-rich-content-dark code,
        .temple-rich-content-dark .ql-code-block {
            background: rgb(2 6 23 / 0.85);
            color: #cbd5e1;
        }

        .temple-rich-content-light h1,
        .temple-rich-content-light h2,
        .temple-rich-content-light h3 {
            color: #0c0a09;
        }

        .temple-rich-content-light a {
            color: #047857;
        }

        .temple-rich-content-light blockquote {
            border-color: rgb(217 119 6 / 0.75);
            color: #57534e;
        }

        .temple-rich-content-light code,
        .temple-rich-content-light .ql-code-block {
            background: #f5f5f4;
            color: #292524;
        }
    </style>
@endonce
