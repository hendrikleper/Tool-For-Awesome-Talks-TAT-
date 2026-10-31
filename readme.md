# Tool for Awesome Talks

Tool for Awesome Talks (TAT) is a lightweight, browser-based presentation tool. It builds a presentation from the files in the `slides` folder and displays slide thumbnails beside a large preview.

## Requirements

- PHP to run the presentation locally.
- The PHP Zip extension to export the presentation as a ZIP file.

## Getting Started

1. Put your presentation files in the repository's `slides` folder.
2. From the repository root, start PHP's built-in web server:

   ```sh
   php -S localhost:8080
   ```

3. Open [http://localhost:8080](http://localhost:8080) in your browser.

Files directly inside `slides` are shown as slides in alphabetical order by filename. Use numbered prefixes such as `01-introduction.md` and `02-agenda.md` to control the order. Supporting assets can go in subfolders, for example `slides/images-slides/`.

## Supported Slides

| File type | Supported formats | Display |
| --- | --- | --- |
| Text | `.md` | Rendered as a Markdown slide |
| Images | `.jpg`, `.png`, `.gif` | Shown as an image slide |
| Video | `.mp4`, `.webm`, `.ogg` | Shown with browser video controls |
| Audio | `.mp3`, `.wav` | Shown with browser audio controls |
| Documents | `.pdf` | Shown in the browser's PDF viewer |

## Writing Text Slides

Create a plain-text file with the `.md` extension in `slides`. Markdown headings, paragraphs, links, lists, images, and other standard Markdown formatting are supported.

```markdown
# Main heading
## Medium heading
### Smaller heading

Regular text with **bold** and *italic* formatting.

- A list item
- Another list item

[A link](https://example.com)
```

For a line break within a paragraph, end the line with two spaces.

### Adding an Image to a Text Slide

Use a path relative to the repository root:

```markdown
![Description](slides/images-slides/porte-1-fleche.png)
```

### Embedding YouTube or Vimeo

Paste an embed iframe into the Markdown file. Replace the URL with the video's embed URL.

```html
<iframe src="https://www.youtube.com/embed/tgbNymZ7vqY" allowfullscreen width="640" height="360"></iframe>
```

For Vimeo, use a URL such as `https://player.vimeo.com/video/13416084`. An internet connection is required to load embedded videos.

## Image Captions

Add a Markdown file beside an image and append `.md` to the image's full filename. For example, `image.jpg` can have a caption file named `image.jpg.md`. The Markdown content of that file is displayed as the image caption.

## Presentation Controls

- Select a thumbnail to preview that slide. The first slide is selected when the presentation opens.
- Use the arrow keys to navigate between slides.
- Use the theme button to switch between dark and light themes. Your selection is saved in the browser.
- Use the information button to view project information.

## Exporting

Select the 📦 button at the bottom of the thumbnail sidebar to download `presentation.zip`. Extract the archive and open `index.html` in a browser to view the presentation without running PHP. The archive includes the slides and required styles and scripts. PHP's Zip extension must be enabled to create the export.

## License

Tool for Awesome Talks is a project by [Sarah Garcin](http://sarahgarcin.com/), released under the [GPL-3.0 license](https://www.gnu.org/licenses/gpl-3.0.html) and adapted for additional needs.
