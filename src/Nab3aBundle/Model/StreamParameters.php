<?php

namespace Nab3aBundle\Model;

use Symfony\Component\Validator\Constraints as Assert;

class StreamParameters
{
    /**
     * @Assert\Language()
     *
     * @var string
     */
    protected $lang;

    /**
     * @Assert\Count(
     *   min = "0",
     *   max = "400"
     * )
     * @Assert\All({
     *     @Assert\NotBlank,
     *     @Assert\Length(min = 5)
     * })
     *
     * @var array
     */
    protected $track;

    /**
     * Is there a way we can put this assertion on the individual items in the
     * collection? If so, these are all integers or they are strings that can be
     * cast as integers.
     *
     * @Assert\Count(
     *   min = "0",
     *   max = "5000"
     * )
     *
     * @var array
     */
    protected $follow;

    /**
     * @Assert\Count(
     *   min = "0",
     *   max = "25"
     * )
     * @Assert\All({
     *     @Assert\Count(
     * })
     *
     * @var array
     */
    protected $locations;

    /**
     * @return mixed
     */
    public function getLang()
    {
        return $this->lang;
    }

    /**
     * @param mixed $lang
     */
    public function setLang($lang)
    {
        $this->lang = $lang;
    }

    /**
     * @return array
     */
    public function getTrack()
    {
        return $this->track;
    }

    /**
     * @param array $track
     */
    public function setTrack($track)
    {
        $this->track = $track;
    }

    /**
     * @return array
     */
    public function getFollow()
    {
        return $this->follow;
    }

    /**
     * @param array $follow
     */
    public function setFollow($follow)
    {
        $this->follow = $follow;
    }

    /**
     * @return array
     */
    public function getLocations()
    {
        return $this->locations;
    }

    /**
     * @param array $location
     */
    public function setLocations($location)
    {
        $this->locations = $location;
    }
}
