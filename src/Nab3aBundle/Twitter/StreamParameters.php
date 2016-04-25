<?php

namespace Nab3aBundle\Twitter;

use Symfony\Component\Validator\Constraints as Assert;

class StreamParameters
{
    /**
     * @Assert\Language()
     *
     * @var string
     */
    protected $language;

    /**
     * @Assert\Count(
     *   min = "0",
     *   max = "400"
     * )
     * @Assert\All({
     *     @Assert\NotBlank,
     *     @Assert\Length(max=60)
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
     * @Assert\All({@Assert\Type("integer")})
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
     *     @Assert\Collection(fields={
     *       {@Assert\Type("float"),@Assert\Range(min=-180,max=180)},
     *       {@Assert\Type("float"),@Assert\Range(min=-90,max=90)},
     *       {@Assert\Type("float"),@Assert\Range(min=-180,max=180)},
     *       {@Assert\Type("float"),@Assert\Range(min=-90,max=90)}
     *     })
     * })
     *
     * @var array
     */
    protected $locations;

    /**
     * @return mixed
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @param mixed $language
     */
    public function setLanguage($language)
    {
        $this->language = $language;
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
