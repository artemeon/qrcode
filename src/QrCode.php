<?php

declare(strict_types=1);

namespace Artemeon\QrCode;

use BaconQrCode\Common\ErrorCorrectionLevel;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

class QrCode
{
    private ErrorCorrectionLevel $correctionLevel;
    private ?string $content = null;

    public static function of(string $content): static
    {
        $instance = new static();
        $instance->content = $content;

        return $instance;
    }

    protected function __construct(private int $size = 160, private int $padding = 4)
    {
        $this->correctionLevel = ErrorCorrectionLevel::Q();
    }

    public function padding(int $padding): self
    {
        $this->padding = $padding;

        return $this;
    }

    public function size(int $size): self
    {
        $this->size = $size;

        return $this;
    }

    public function store(string $directory, string $fileNamePrefix = 'qr'): string
    {
        $fileName = $directory . DIRECTORY_SEPARATOR . $this->getFileName($fileNamePrefix);

        $this->generate($fileName);

        return $fileName;
    }

    private function generate(string $fileName): void
    {
        (new Writer(
            new ImageRenderer(
                new RendererStyle($this->size, $this->padding),
                new SvgImageBackEnd(),
            ),
        ))->writeFile(
            content: $this->content ?? '',
            filename: $fileName,
            ecLevel: $this->correctionLevel,
        );
    }

    public function getFileName(string $prefix): string
    {
        return $prefix . md5($this->content . $this->correctionLevel->name() . $this->size . $this->padding) . '.svg';
    }
}
