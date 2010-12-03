package org.aerial.services
{
	import org.aerial.rpc.AbstractService;
	
	import org.aerial.vo.TopicTagVO;
	import org.aerial.bootstrap.Aerial;

	public class TopicTagService extends AbstractService
	{
		public function TopicTagService()
		{
			super("TopicTagService", Aerial.SERVER_URL, TopicTagVO);
		}
	}
}