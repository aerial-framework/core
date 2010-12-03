package org.aerial.services
{
	import org.aerial.rpc.AbstractService;
	
	import org.aerial.vo.TagVO;
	import org.aerial.bootstrap.Aerial;

	public class TagService extends AbstractService
	{
		public function TagService()
		{
			super("TagService", Aerial.SERVER_URL, TagVO);
		}
	}
}