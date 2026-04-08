<?php

namespace App\Livewire\Video;

use App\Enums\VideoType;
use App\Models\Properties;
use App\Models\PropertyVideos;
use BenSampo\Embed\Rules\EmbeddableUrl;
use BenSampo\Embed\Services\Vimeo;
use BenSampo\Embed\Services\YouTube;
use Illuminate\Http\UploadedFile;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;

class Add extends Component
{
    use LivewireAlert;
    use WithFileUploads;

    public bool $show = false;

    public $property;

    public $showUploadOption = 'Youtube';

    public $video_url;

    public $video_type;

    public $videos;

    #[On('open-video-add')]
    public function openModal(int $propertyId): void
    {
        $this->property = Properties::find($propertyId);
        $this->show = true;
    }

    public function closeModal(): void
    {
        $this->show = false;
        $this->reset(['video_url', 'videos', 'showUploadOption']);
        $this->showUploadOption = 'Youtube';
    }

    public function save()
    {
        if ($this->showUploadOption == 'Youtube') {
            $this->validate([
                'video_url' => [
                    'required',
                    (new EmbeddableUrl)->allowedServices([
                        YouTube::class,
                    ]),
                ],
            ]);
            $this->video_type = VideoType::YouTube;
        } elseif ($this->showUploadOption == 'Vimeo') {
            $this->validate([
                'video_url' => [
                    'required',
                    (new EmbeddableUrl)->allowedServices([
                        Vimeo::class,
                    ]),
                ],
            ]);
            $this->video_type = VideoType::Vimeo;
        } else {
            // Validate video files server-side
            $this->validate([
                'videos.*' => 'required|file|mimes:mp4,mov,avi,wmv,flv,webm|max:102400', // 100MB max
            ]);

            foreach ($this->videos as $video) {
                // Additional MIME type verification
                if (!$this->validateMimeType($video, [
                    'video/mp4',
                    'video/quicktime',
                    'video/x-msvideo',
                    'video/x-ms-wmv',
                    'video/x-flv',
                    'video/webm'
                ])) {
                    $this->alert('error', 'Invalid file type detected. Only video files are allowed.');
                    return;
                }

                $laravelFile = new UploadedFile(
                    $video['path'],
                    $video['name'],
                    $video['type'] ?? 'video/mp4',
                    null,
                    true
                );
                $path = uploadS3Image('property_videos', $laravelFile);
            }

            $this->video_url = $path ?? '';
            $this->video_type = VideoType::Dropzone;

            $this->reset('videos');
        }

        $property_video = new PropertyVideos;
        $property_video->property_id = $this->property->id;
        $property_video->video_type = $this->video_type;
        $property_video->video_url = $this->video_url;
        $property_video->save();

        $this->alert('success', 'Property Video added successfully.');

        $this->dispatch('refresh');
        $this->show = false;
        $this->reset(['video_url', 'videos', 'showUploadOption']);
        $this->showUploadOption = 'Youtube';
    }

    public function render()
    {
        return view('livewire.video.add');
    }

    /**
     * Validate file MIME type to prevent spoofing
     */
    private function validateMimeType($file, array $allowedMimeTypes): bool
    {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['path']);
        finfo_close($finfo);

        return in_array($mimeType, $allowedMimeTypes);
    }
}
